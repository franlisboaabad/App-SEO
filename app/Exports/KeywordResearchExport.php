<?php

namespace App\Exports;

use App\Models\KeywordResearch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KeywordResearchExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $siteId;

    public function __construct($siteId = null)
    {
        $this->siteId = $siteId;
    }

    public function collection()
    {
        $query = KeywordResearch::with('site');

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
            'Fuente',
            'Cluster/Tema',
            'Intención',
            'Volumen de Búsqueda',
            'Dificultad',
            'CPC',
            'Posición Actual',
            'Clics',
            'Impresiones',
            'CTR (%)',
            'Score de Tendencia',
            'Estado',
            'Notas',
        ];
    }

    public function map($research): array
    {
        $intentLabels = [
            'transactional' => 'Transaccional',
            'commercial' => 'Comercial',
            'navigational' => 'Navegacional',
            'informational' => 'Informativa',
        ];

        return [
            $research->site->nombre ?? 'N/A',
            $research->keyword,
            ucfirst($research->source ?? 'N/A'),
            $research->cluster ?? 'N/A',
            $intentLabels[$research->intent] ?? 'N/A',
            $research->search_volume ? number_format($research->search_volume) : 'N/A',
            $research->difficulty ? number_format($research->difficulty, 1) : 'N/A',
            $research->cpc ? '$' . number_format($research->cpc, 2) : 'N/A',
            $research->current_position ? number_format($research->current_position, 1) : 'N/A',
            $research->clicks ?? 0,
            $research->impressions ?? 0,
            $research->ctr ? number_format($research->ctr * 100, 2) : '0.00',
            $research->trend_score ?? 'N/A',
            $research->is_tracked ? 'Trackeada' : 'No trackeada',
            $research->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:O1')->applyFromArray([
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
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(50);

        // Congelar primera fila
        $sheet->freezePane('A2');

        return [];
    }

    public function title(): string
    {
        return 'Investigación Keywords';
    }
}

