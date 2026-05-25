<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function __invoke(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $range    = $request->get('range', '7');   // days

        $daily   = $this->reportService->dailySummary($tenantId);
        $weekly  = $this->reportService->weeklySummary($tenantId);
        $riders  = $this->reportService->riderPerformance($tenantId);

        return Inertia::render('Admin/Analytics/Index', [
            'daily'         => $daily,
            'weekly'        => $weekly,
            'riderPerformance' => $riders,
            'range'         => $range,
        ]);
    }
}
