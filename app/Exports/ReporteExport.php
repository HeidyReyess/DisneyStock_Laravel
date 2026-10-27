<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteExport implements FromView, ShouldAutoSize
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
}
