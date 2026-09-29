<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImpactMetricRequest;
use App\Http\Requests\UpdateImpactMetricRequest;
use App\Models\AuditLog;
use App\Models\ImpactMetric;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ImpactMetricController extends Controller
{
    /**
     * Display a listing of impact and performance metrics.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ImpactMetric::class);

        $selectedYear = (int) ($request->input('year', date('Y')));

        $metrics = ImpactMetric::with('recorder')
            ->where('year', $selectedYear)
            ->get();

        $availableYears = ImpactMetric::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->all();
        if (! in_array((int) date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
        }

        // Summary calculations
        $totalMetricsCount = $metrics->count();
        $achievedCount = $metrics->filter(fn ($m) => $m->achievement_percentage >= 100)->count();
        $averageAchievement = $metrics->isNotEmpty() ? round($metrics->avg('achievement_percentage'), 1) : 0;

        $categories = [
            ImpactMetric::CAT_STARTUP_GROWTH => 'Pertumbuhan Startup',
            ImpactMetric::CAT_PATENT_HKI => 'Paten & HKI',
            ImpactMetric::CAT_COMMERCIALIZATION => 'Komersialisasi & Royalti',
            ImpactMetric::CAT_WORKFORCE => 'Penyerapan Tenaga Kerja',
            ImpactMetric::CAT_FUNDING_INVESTMENT => 'Pendanaan & Investasi',
            ImpactMetric::CAT_SOCIO_ECONOMIC => 'Dampak Sosial & Industri',
        ];

        return view('admin.impact.index', compact(
            'metrics',
            'selectedYear',
            'availableYears',
            'totalMetricsCount',
            'achievedCount',
            'averageAchievement',
            'categories'
        ));
    }

    /**
     * Show form for creating a new impact metric.
     */
    public function create(): View
    {
        Gate::authorize('manage', ImpactMetric::class);

        return view('admin.impact.create');
    }

    /**
     * Store a newly created impact metric.
     */
    public function store(StoreImpactMetricRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['recorded_by'] = $request->user()->id;

        $metric = ImpactMetric::create($data);

        AuditLogService::log(
            AuditLog::MODULE_IMPACT,
            AuditLog::ACTION_CREATE,
            "Menambahkan indikator capaian dampak: {$metric->metric_name} ({$metric->year})",
            $metric,
            null,
            $metric->toArray()
        );

        return redirect()
            ->route('admin.impact.index', ['year' => $metric->year])
            ->with('success', "Indikator kinerja dampak [{$metric->metric_name}] berhasil ditambahkan.");
    }

    /**
     * Show form for editing the specified impact metric.
     */
    public function edit(ImpactMetric $impact): View
    {
        Gate::authorize('manage', ImpactMetric::class);

        return view('admin.impact.edit', ['metric' => $impact]);
    }

    /**
     * Update the specified impact metric.
     */
    public function update(UpdateImpactMetricRequest $request, ImpactMetric $impact): RedirectResponse
    {
        $oldValues = $impact->toArray();
        $impact->update($request->validated());

        AuditLogService::log(
            AuditLog::MODULE_IMPACT,
            AuditLog::ACTION_UPDATE,
            "Memperbarui indikator capaian dampak: {$impact->metric_name} ({$impact->year})",
            $impact,
            $oldValues,
            $impact->fresh()->toArray()
        );

        return redirect()
            ->route('admin.impact.index', ['year' => $impact->year])
            ->with('success', "Indikator [{$impact->metric_name}] berhasil diperbarui.");
    }

    /**
     * Remove the specified impact metric.
     */
    public function destroy(ImpactMetric $impact): RedirectResponse
    {
        Gate::authorize('manage', ImpactMetric::class);

        $name = $impact->metric_name;
        $year = $impact->year;
        $impact->delete();

        AuditLogService::log(
            AuditLog::MODULE_IMPACT,
            AuditLog::ACTION_DELETE,
            "Menghapus indikator capaian dampak: {$name} ({$year})",
            $impact,
            ['id' => $impact->id, 'name' => $name],
            null
        );

        return redirect()
            ->route('admin.impact.index', ['year' => $year])
            ->with('success', "Indikator [{$name}] berhasil dihapus.");
    }
}
