<?php

namespace App\Exports;

use App\Models\Keyword;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KeywordsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $siteId;

    public function __construct($siteId = null)
    {
        $this->siteId = $siteId;
    }

    public function collection()
    {
        $query = Keyword::with('site');

        if ($this->siteId) {
            $query->where('site_id', $this->siteId);
        }

        return $query->orderBy('site_id')->orderBy('keyword')->get();
    }

    public function headings(): array
    {
        return [
            'Sitio',
            'Keyword',
            'URL Objetivo',
            'Posición Actual',
            'Posición Anterior',
            'Última Verificación',
            'Estado',
            'Notas',
        ];
    }

    public function map($keyword): array
    {
        return [
            $keyword->site->nombre ?? 'N/A',
            $keyword->keyword,
            $keyword->target_url ?? 'N/A',
            $keyword->current_position ?? 'N/A',
            $keyword->previous_position ?? 'N/A',
            $keyword->last_checked ? $keyword->last_checked->format('d/m/Y H:i') : 'N/A',
            $keyword->is_active ? 'Activa' : 'Inactiva',
            $keyword->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(50);

        // Congelar primera fila
        $sheet->freezePane('A2');

        return [];
    }

    public function title(): string
    {
        return 'Keywords';
    }
}

