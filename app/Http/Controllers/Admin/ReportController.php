<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Dashboard admin dengan ringkasan.
     */
    public function dashboard()
    {
        $summary = $this->reportService->dashboardSummary();
        return view('admin.dashboard', $summary);
    }

    /**
     * Halaman laporan penjualan detail.
     */
    public function index(Request $request)
    {
        $report = $this->reportService->salesReport(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return view('admin.reports.index', $report);
    }
}
