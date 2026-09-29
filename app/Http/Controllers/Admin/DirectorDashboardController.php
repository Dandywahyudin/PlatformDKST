<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ConsultationService;
use App\Models\ImpactMetric;
use App\Models\Program;
use App\Models\ProgramEvaluation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectorDashboardController extends Controller
{
    /**
     * Display the Executive Director Dashboard.
     */
    public function index(Request $request): View
    {
        $selectedYear = (int) $request->input('year', date('Y'));

        // 1. KPI Cards
        $totalPrograms = Program::count();
        $inProgressPrograms = Program::where('status', Program::STATUS_IN_PROGRESS)->count();
        $completedPrograms = Program::where('status', Program::STATUS_COMPLETED)->count();

        // Programs requiring attention: REJECTED, SUBMITTED/UNDER_REVIEW, or IN_PROGRESS with progress < 40%
        $attentionPrograms = Program::with(['pic', 'latestEvaluation'])
            ->where(function ($q) {
                $q->whereIn('status', [Program::STATUS_REJECTED, Program::STATUS_UNDER_REVIEW, Program::STATUS_SUBMITTED])
                    ->orWhere(function ($sub) {
                        $sub->where('status', Program::STATUS_IN_PROGRESS)
                            ->where('progress', '<', 50);
                    });
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        $attentionProgramsCount = Program::whereIn('status', [Program::STATUS_REJECTED, Program::STATUS_UNDER_REVIEW, Program::STATUS_SUBMITTED])
            ->orWhere(function ($sub) {
                $sub->where('status', Program::STATUS_IN_PROGRESS)->where('progress', '<', 50);
            })
            ->count();

        $avgProgress = round(Program::whereIn('status', [Program::STATUS_IN_PROGRESS, Program::STATUS_COMPLETED])->avg('progress') ?? 0, 1);

        // 2. Program Status Distribution
        $statusDistribution = [
            'DRAFT' => Program::where('status', Program::STATUS_DRAFT)->count(),
            'SUBMITTED' => Program::where('status', Program::STATUS_SUBMITTED)->count(),
            'UNDER_REVIEW' => Program::where('status', Program::STATUS_UNDER_REVIEW)->count(),
            'APPROVED' => Program::where('status', Program::STATUS_APPROVED)->count(),
            'IN_PROGRESS' => Program::where('status', Program::STATUS_IN_PROGRESS)->count(),
            'COMPLETED' => Program::where('status', Program::STATUS_COMPLETED)->count(),
            'REJECTED' => Program::where('status', Program::STATUS_REJECTED)->count(),
        ];

        // 3. Monitoring Health Breakdown
        $activePrograms = Program::where('status', Program::STATUS_IN_PROGRESS)->get();
        $activeCount = $activePrograms->count();
        $onTrackCount = $activePrograms->filter(fn ($p) => $p->progress >= 60)->count();
        $atRiskCount = $activePrograms->filter(fn ($p) => $p->progress >= 25 && $p->progress < 60)->count();
        $delayedCount = $activePrograms->filter(fn ($p) => $p->progress < 25)->count();

        $monitoringHealth = [
            'on_track' => $onTrackCount,
            'on_track_pct' => $activeCount > 0 ? round(($onTrackCount / $activeCount) * 100) : 0,
            'at_risk' => $atRiskCount,
            'at_risk_pct' => $activeCount > 0 ? round(($atRiskCount / $activeCount) * 100) : 0,
            'delayed' => $delayedCount,
            'delayed_pct' => $activeCount > 0 ? round(($delayedCount / $activeCount) * 100) : 0,
        ];

        // 4. Kinerja & Dampak (Impact Metrics)
        $impactMetrics = ImpactMetric::where('year', $selectedYear)->get();
        $impactTotalCount = $impactMetrics->count();
        $impactAchievedCount = $impactMetrics->filter(fn ($m) => $m->achievement_percentage >= 100)->count();
        $impactUnachievedCount = $impactTotalCount - $impactAchievedCount;
        $impactAvgAchievement = $impactTotalCount > 0 ? round($impactMetrics->avg('achievement_percentage'), 1) : 0;

        // 5. Monitoring & Evaluasi Terbaru
        $recentEvaluations = ProgramEvaluation::with(['program.pic', 'evaluator'])
            ->latest('evaluation_date')
            ->take(5)
            ->get();

        // 6. Layanan & Konsultasi Summary
        $servicesSummary = [
            'active' => ConsultationService::whereIn('status', [
                ConsultationService::STATUS_PENDING,
                ConsultationService::STATUS_IN_REVIEW,
                ConsultationService::STATUS_SCHEDULED,
            ])->count(),
            'completed' => ConsultationService::where('status', ConsultationService::STATUS_COMPLETED)->count(),
            'pending_action' => ConsultationService::whereIn('status', [
                ConsultationService::STATUS_PENDING,
                ConsultationService::STATUS_IN_REVIEW,
            ])->count(),
        ];

        $priorityConsultations = ConsultationService::with(['applicant', 'consultant'])
            ->whereIn('status', [ConsultationService::STATUS_PENDING, ConsultationService::STATUS_SCHEDULED])
            ->latest()
            ->take(4)
            ->get();

        // 7. Recent Activities Stream
        $recentActivities = AuditLog::with('user')
            ->whereIn('module', [
                AuditLog::MODULE_PROGRAMS,
                AuditLog::MODULE_APPROVALS,
                AuditLog::MODULE_MONEV,
                AuditLog::MODULE_SERVICES,
                AuditLog::MODULE_IMPACT,
            ])
            ->latest()
            ->take(6)
            ->get();

        $availableYears = ImpactMetric::select('year')->distinct()->orderBy('year', 'desc')->pluck('year')->all();
        if (! in_array((int) date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
        }

        return view('admin.director.dashboard', compact(
            'selectedYear',
            'availableYears',
            'totalPrograms',
            'inProgressPrograms',
            'completedPrograms',
            'attentionProgramsCount',
            'avgProgress',
            'statusDistribution',
            'monitoringHealth',
            'attentionPrograms',
            'impactMetrics',
            'impactTotalCount',
            'impactAchievedCount',
            'impactUnachievedCount',
            'impactAvgAchievement',
            'recentEvaluations',
            'servicesSummary',
            'priorityConsultations',
            'recentActivities'
        ));
    }
}
