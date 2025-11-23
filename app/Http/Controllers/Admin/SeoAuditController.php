<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\SeoAudit;
use App\Jobs\RunSeoAudit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeoAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.audits.index')->only('index');
        $this->middleware('can:admin.audits.show')->only('show');
        $this->middleware('can:admin.audits.run')->only('runAudit');
    }

    /**
     * Ejecutar auditoría para una URL
     */
    public function runAudit(Request $request, Site $site)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            // Encolar auditoría
            RunSeoAudit::dispatch($site, $request->url);

            return back()->with('success', 'Auditoría encolada correctamente. Se procesará en segundo plano.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al encolar auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de auditorías de un sitio
     */
    public function index(Site $site)
    {
        $audits = $site->seoAudits()
            ->with('result')
            ->latest()
            ->paginate(20);

        return view('admin.sites.audits', compact('site', 'audits'));
    }

    /**
     * Ver detalles de una auditoría
     */
    public function show(SeoAudit $audit)
    {
        $audit->load('result', 'site');

        return view('admin.sites.audit-detail', compact('audit'));
    }

    /**
     * Exportar links internos a Excel
     */
    public function exportInternalLinks(SeoAudit $audit)
    {
        if (!$audit->result || empty($audit->result->internal_links)) {
            return back()->with('error', 'No hay links internos para exportar.');
        }

        $data = array_map(function($link) {
            return [
                'URL' => $link['url'] ?? '',
                'Texto del Link' => $link['text'] ?? '',
                'Href Original' => $link['href'] ?? '',
            ];
        }, $audit->result->internal_links);

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return ['URL', 'Texto del Link', 'Href Original'];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true]],
                ];
            }
        }, "links_internos_audit_{$audit->id}.xlsx");
    }

    /**
     * Exportar links externos a Excel
     */
    public function exportExternalLinks(SeoAudit $audit)
    {
        if (!$audit->result || empty($audit->result->external_links)) {
            return back()->with('error', 'No hay links externos para exportar.');
        }

        $data = array_map(function($link) {
            return [
                'URL' => $link['url'] ?? '',
                'Texto del Link' => $link['text'] ?? '',
                'Href Original' => $link['href'] ?? '',
            ];
        }, $audit->result->external_links);

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return ['URL', 'Texto del Link', 'Href Original'];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true]],
                ];
            }
        }, "links_externos_audit_{$audit->id}.xlsx");
    }

    /**
     * Exportar links rotos a Excel
     */
    public function exportBrokenLinks(SeoAudit $audit)
    {
        if (!$audit->result || empty($audit->result->broken_links)) {
            return back()->with('error', 'No hay links rotos para exportar.');
        }

        $data = array_map(function($link) {
            return [
                'URL' => $link['url'] ?? '',
                'Texto del Link' => $link['text'] ?? '',
                'Href Original' => $link['href'] ?? '',
                'Status Code' => $link['status_code'] ?? 'N/A',
            ];
        }, $audit->result->broken_links);

        return Excel::download(new class($data) implements FromArray, WithHeadings, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return ['URL', 'Texto del Link', 'Href Original', 'Status Code'];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true]],
                ];
            }
        }, "links_rotos_audit_{$audit->id}.xlsx");
    }
}
