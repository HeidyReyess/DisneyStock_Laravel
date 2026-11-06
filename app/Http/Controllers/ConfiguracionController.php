<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ConfiguracionController extends Controller
{
    // GET /configuracion
    public function index()
    {
        // Carga todas las claves en un array asociativo para la vista
        $config = Configuracion::all()->pluck('valor', 'clave');

        // Carga lista de copias de seguridad existentes
        $backupDir = storage_path('app/backups');
        $backups = [];
        if (File::exists($backupDir)) {
            $archivos = File::glob($backupDir . '/backup_*.sql') ?: [];
            usort($archivos, fn($a, $b) => filemtime($b) <=> filemtime($a));
            foreach ($archivos as $archivo) {
                $nombre = basename($archivo);
                $backups[] = [
                    'nombre' => $nombre,
                    'fecha'  => date('d/m/Y H:i', filemtime($archivo)),
                    'tamano' => $this->formatearTamano(filesize($archivo)),
                ];
            }
        }

        return view('admin.configuracion.index', compact('config', 'backups'));
    }

    // POST /configuracion
    public function update(Request $request)
    {
        $request->validate([
            'nombre_negocio'               => ['required', 'string', 'max:150'],
            'nit_negocio'                  => ['nullable', 'string', 'max:50'],
            'direccion_negocio'            => ['nullable', 'string', 'max:200'],
            'telefono_negocio'             => ['nullable', 'string', 'max:50'],
            'prefijo_factura'              => ['required', 'string', 'max:10'],
            'porcentaje_iva'               => ['required', 'numeric', 'min:0', 'max:100'],
            'descuento_maximo_porcentaje'  => ['required', 'numeric', 'min:0', 'max:100'],
            'stock_minimo_defecto'         => ['required', 'integer', 'min:1'],
        ], [
            'nombre_negocio.required'              => 'El nombre del negocio es obligatorio.',
            'nombre_negocio.max'                   => 'El nombre no puede superar 150 caracteres.',
            'prefijo_factura.required'             => 'El prefijo de factura es obligatorio.',
            'prefijo_factura.max'                  => 'El prefijo no puede superar 10 caracteres.',
            'porcentaje_iva.required'              => 'El porcentaje de IVA es obligatorio.',
            'porcentaje_iva.numeric'               => 'El IVA debe ser un número.',
            'porcentaje_iva.min'                   => 'El IVA no puede ser negativo.',
            'porcentaje_iva.max'                   => 'El IVA no puede superar el 100%.',
            'descuento_maximo_porcentaje.required' => 'El descuento máximo es obligatorio.',
            'descuento_maximo_porcentaje.numeric'  => 'El descuento máximo debe ser un número.',
            'descuento_maximo_porcentaje.min'      => 'El descuento máximo no puede ser negativo.',
            'descuento_maximo_porcentaje.max'      => 'El descuento máximo no puede superar el 100%.',
            'stock_minimo_defecto.required'        => 'El stock mínimo por defecto es obligatorio.',
            'stock_minimo_defecto.integer'         => 'El stock mínimo debe ser un número entero.',
            'stock_minimo_defecto.min'             => 'El stock mínimo debe ser al menos 1.',
        ]);

        $claves = [
            'nombre_negocio',
            'nit_negocio',
            'direccion_negocio',
            'telefono_negocio',
            'prefijo_factura',
            'porcentaje_iva',
            'descuento_maximo_porcentaje',
            'stock_minimo_defecto',
        ];

        foreach ($claves as $clave) {
            Configuracion::establecer($clave, $request->input($clave, ''));
        }

        return redirect()->route('configuracion.index')
            ->with('success', 'Configuración guardada correctamente.');
    }

    // POST /configuracion/backups — generar copia de seguridad con mysqldump
    public function generarBackup()
    {
        if (!Auth::user()->esAdmin()) abort(403);

        $mysqldumpPath = env('MYSQLDUMP_PATH');
        if (empty($mysqldumpPath) || !file_exists($mysqldumpPath)) {
            return redirect()->route('configuracion.index')
                ->with('error', "No se encontró el ejecutable mysqldump en la ruta configurada (" . ($mysqldumpPath ?: 'no definida') . "). Verifica la variable MYSQLDUMP_PATH en el archivo .env.");
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $nombreArchivo = 'backup_' . now()->format('Y-m-d_H-i') . '.sql';
        $destino = $backupDir . DIRECTORY_SEPARATOR . $nombreArchivo;

        $host     = config('database.connections.mysql.host', '127.0.0.1');
        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }
        $port     = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'disney_stock');
        $username = config('database.connections.mysql.username', 'root');
        $password = config('database.connections.mysql.password', '');

        // Comando mysqldump que incluye estructura completa (CREATE TABLE) y datos
        $comando = [
            $mysqldumpPath,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            '--add-drop-table',
            '--create-options',
            '--quick',
            '--extended-insert',
        ];
        if (!empty($password)) {
            $comando[] = "--password={$password}";
        }
        $comando[] = $database;

        try {
            $proceso = new Process($comando);
            $proceso->setTimeout(180);
            $proceso->run();

            if (!$proceso->isSuccessful()) {
                $errorMsg = trim($proceso->getErrorOutput());
                return redirect()->route('configuracion.index')
                    ->with('error', "Error al generar la copia de seguridad: " . ($errorMsg ?: 'mysqldump falló sin mensaje detallado.'));
            }

            File::put($destino, $proceso->getOutput());

            return redirect()->route('configuracion.index')
                ->with('success', "Copia de seguridad \"{$nombreArchivo}\" generada correctamente.");
        } catch (\Throwable $e) {
            return redirect()->route('configuracion.index')
                ->with('error', "Error inesperado al generar la copia de seguridad: {$e->getMessage()}");
        }
    }

    // GET /configuracion/backups/{archivo}/descargar
    public function descargarBackup($archivo)
    {
        if (!Auth::user()->esAdmin()) abort(403);

        if (basename($archivo) !== $archivo || !preg_match('/^backup_[0-9_-]+\.sql$/', $archivo)) {
            abort(404);
        }

        $path = storage_path('app/backups/' . $archivo);
        if (!File::exists($path)) {
            return redirect()->route('configuracion.index')
                ->with('error', 'El archivo de copia de seguridad no existe o fue eliminado.');
        }

        return response()->download($path, $archivo, [
            'Content-Type' => 'application/sql',
        ]);
    }

    // DELETE /configuracion/backups/{archivo}
    public function eliminarBackup($archivo)
    {
        if (!Auth::user()->esAdmin()) abort(403);

        if (basename($archivo) !== $archivo || !preg_match('/^backup_[0-9_-]+\.sql$/', $archivo)) {
            abort(404);
        }

        $path = storage_path('app/backups/' . $archivo);
        if (!File::exists($path)) {
            return redirect()->route('configuracion.index')
                ->with('error', 'El archivo de copia de seguridad no existe.');
        }

        File::delete($path);

        return redirect()->route('configuracion.index')
            ->with('success', "Copia de seguridad \"{$archivo}\" eliminada correctamente.");
    }

    private function formatearTamano($bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        }
        return $bytes . ' B';
    }
}
