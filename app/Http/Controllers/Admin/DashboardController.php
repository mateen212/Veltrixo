<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function __invoke(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;
        $from = \Carbon\Carbon::parse($request->get('from', now()->startOfMonth()));
        $to   = \Carbon\Carbon::parse($request->get('to', now()));

        return Inertia::render('Admin/Dashboard', [
            'metrics' => $this->analytics->getDashboardMetrics($tenantId, $from, $to),
            'from'    => $from->toDateString(),
            'to'      => $to->toDateString(),
        ]);
    }
}
