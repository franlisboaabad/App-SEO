<?php

namespace App\Exports;

use App\Models\SeoMetric;
use App\Models\Site;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class SeoMetricsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $siteId;
    protected $startDate;
    protected $endDate;

    public function __construct($siteId = null, $startDate = null, $endDate = null)
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
    }

    public function collection()
    {
        $query = SeoMetric::with('site')
            ->whereBetween('date', [$this->startDate, $this->endDate]);

        if ($this->siteId) {
            $query->where('site_id', $this->siteId);
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('site_id')
            ->orderBy('keyword')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sitio',
            'Fecha',
            'Keyword',
            'Posición',
            'Clics',
            'Impresiones',
            'CTR (%)',
            'URL',
        ];
    }

    public function map($metric): array
    {
        return [
            $metric->site->nombre ?? 'N/A',
            $metric->date ? Carbon::parse($metric->date)->format('d/m/Y') : 'N/A',
            $metric->keyword ?? 'N/A',
            $metric->position ? number_format($metric->position, 1) : 'N/A',
            $metric->clicks ?? 0,
            $metric->impressions ?? 0,
            $metric->ctr ? number_format($metric->ctr * 100, 2) : '0.00',
            $metric->url ?? 'N/A',
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
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(40);

        // Congelar primera fila
        $sheet->freezePane('A2');

        return [];
    }

    public function title(): string
    {
        return 'Métricas GSC';
    }
}

