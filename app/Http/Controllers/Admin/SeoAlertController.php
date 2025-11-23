<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoAlert;
use App\Models\Site;
use App\Services\AlertService;
use Illuminate\Http\Request;

class SeoAlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar alertas
     */
    public function index(Request $request)
    {
        $siteId = $request->get('site_id');
        $type = $request->get('type');
        $severity = $request->get('severity');
        $unreadOnly = $request->get('unread_only', false);

        $query = SeoAlert::with('site')->latest();

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($severity) {
            $query->where('severity', $severity);
        }

        if ($unreadOnly) {
            $query->unread();
        }

        $alerts = $query->paginate(20);
        $sites = Site::active()->get();

        // Estadísticas
        $totalAlerts = SeoAlert::count();
        $unreadAlerts = SeoAlert::unread()->count();
        $criticalAlerts = SeoAlert::bySeverity('critical')->unread()->count();

        return view('admin.alerts.index', compact('alerts', 'sites', 'siteId', 'type', 'severity', 'unreadOnly', 'totalAlerts', 'unreadAlerts', 'criticalAlerts'));
    }

    /**
     * Marcar alerta como leída
     */
    public function markAsRead(SeoAlert $alert)
    {
        $alert->markAsRead();
        return back()->with('success', 'Alerta marcada como leída.');
    }

    /**
     * Marcar alerta como resuelta
     */
    public function markAsResolved(SeoAlert $alert)
    {
        $alert->markAsResolved();
        return back()->with('success', 'Alerta marcada como resuelta.');
    }

    /**
     * Marcar todas como leídas
     */
    public function markAllAsRead(Request $request)
    {
        $siteId = $request->get('site_id');

        $query = SeoAlert::unread();
        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $count = $query->update(['is_read' => true]);

        return back()->with('success', "Se marcaron {$count} alertas como leídas.");
    }

    /**
     * Detectar cambios automáticamente
     */
    public function detectChanges(Request $request)
    {
        $alertService = new AlertService();
        $siteId = $request->get('site_id');

        $site = $siteId ? Site::findOrFail($siteId) : null;

        $positionAlerts = $alertService->detectPositionChanges($site);
        $trafficAlerts = $alertService->detectTrafficDrops($site);

        return back()->with('success', "Se crearon {$positionAlerts} alertas de posición y {$trafficAlerts} alertas de tráfico.");
    }

    /**
     * Obtener contador de alertas no leídas (para AJAX)
     */
    public function getUnreadCount()
    {
        $count = SeoAlert::unread()->count();
        $criticalCount = SeoAlert::unread()->bySeverity('critical')->count();

        return response()->json([
            'total' => $count,
            'critical' => $criticalCount,
        ]);
    }
}
