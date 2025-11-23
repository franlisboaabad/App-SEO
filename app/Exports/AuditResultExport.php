<?php

namespace App\Exports;

use App\Models\SeoAudit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AuditResultExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $siteId;

    public function __construct($siteId = null)
    {
        $this->siteId = $siteId;
    }

    public function collection()
    {
        $query = SeoAudit::with('site', 'result')
            ->where('status', 'completed')
            ->whereHas('result');

        if ($this->siteId) {
            $query->where('site_id', $this->siteId);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Sitio',
            'URL Auditada',
            'Fecha',
            'Score SEO',
            'Status Code',
            'TTFB (s)',
            'Title',
            'Meta Description',
            'H1',
            'Palabras',
            'Links Internos',
            'Links Externos',
            'Links Rotos',
            'Imágenes sin ALT',
            'Errores',
            'Advertencias',
        ];
    }

    public function map($audit): array
    {
        $result = $audit->result;

        return [
            $audit->site->nombre ?? 'N/A',
            $audit->url,
            $audit->created_at ? $audit->created_at->format('d/m/Y H:i') : 'N/A',
            $result->seo_score ?? 'N/A',
            $result->status_code ?? 'N/A',
            $result->ttfb ? number_format($result->ttfb, 2) : 'N/A',
            $result->title ?? 'N/A',
            $result->meta_description ? substr($result->meta_description, 0, 100) . '...' : 'N/A',
            $result->h1 ?? 'N/A',
            $result->word_count ?? 0,
            is_array($result->internal_links) ? count($result->internal_links) : 0,
            is_array($result->external_links) ? count($result->external_links) : 0,
            is_array($result->broken_links) ? count($result->broken_links) : 0,
            is_array($result->images_without_alt) ? count($result->images_without_alt) : 0,
            is_array($result->errors) ? count($result->errors) : 0,
            is_array($result->warnings) ? count($result->warnings) : 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:P1')->applyFromArray([
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
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(50);
        $sheet->getColumnDimension('H')->setWidth(50);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(12);
        $sheet->getColumnDimension('P')->setWidth(15);

        // Congelar primera fila
        $sheet->freezePane('A2');

        return [];
    }

    public function title(): string
    {
        return 'Auditorías SEO';
    }
}

