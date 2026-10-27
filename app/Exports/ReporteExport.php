<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ReporteExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        protected string $tipo,
        protected array $data
    ) {}

    public function view(): View
    {
        return view('admin.reportes.excel', array_merge([
            'tipo' => $this->tipo,
        ], $this->data));
    }

    public function columnFormats(): array
    {
        $formatoDinero = '"$ "#,##0';
        $formatoNumero = '#,##0';

        return match ($this->tipo) {
            'ventas' => [
                'B' => $formatoNumero,
                'C' => $formatoDinero,
                'D' => $formatoDinero,
                'E' => $formatoDinero,
            ],
            'inventario' => [
                'C' => $formatoNumero,
                'D' => $formatoNumero,
                'F' => $formatoDinero,
                'G' => $formatoDinero,
            ],
            'stock_bajo' => [
                'C' => $formatoNumero,
                'D' => $formatoNumero,
                'E' => $formatoNumero,
            ],
            'movimientos' => [
                'C' => $formatoNumero,
            ],
            default => [],
        };
    }
}
