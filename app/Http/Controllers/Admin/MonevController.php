<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramEvaluationRequest;
use App\Http\Requests\UpdateProgramEvaluationRequest;
use App\Models\Program;
use App\Models\ProgramEvaluation;
use App\Models\User;
use App\Services\ProgramEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MonevController extends Controller
{
    /**
     * Display a listing of monitoring & evaluation reports.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProgramEvaluation::class);

        $query = ProgramEvaluation::with(['program.pic', 'evaluator'])->latest('evaluation_date');

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->input('program_id'));
        }

        if ($request->filled('evaluation_period')) {
            $query->where('evaluation_period', $request->input('evaluation_period'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('program', function ($pq) use ($search) {
                $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $evaluations = $query->paginate(10)->withQueryString();
        $programs = Program::orderBy('name')->get();

        $stats = [
            'total' => ProgramEvaluation::count(),
            'avg_score' => round(ProgramEvaluation::avg('score') ?? 0, 1),
            'avg_progress' => round(ProgramEvaluation::avg('progress_percentage') ?? 0, 1),
            'total_realization' => ProgramEvaluation::sum('budget_realization'),
        ];

        return view('admin.monev.index', compact('evaluations', 'programs', 'stats'));
    }

    /**
     * Show the form for creating a new evaluation report.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', ProgramEvaluation::class);

        $programs = Program::orderBy('name')->get();
        $evaluators = User::active()->orderBy('name')->get();
        $selectedProgramId = $request->input('program_id');

        return view('admin.monev.create', compact('programs', 'evaluators', 'selectedProgramId'));
    }

    /**
     * Store a newly created evaluation report.
     */
    public function store(StoreProgramEvaluationRequest $request, ProgramEvaluationService $monevService): RedirectResponse
    {
        $evaluation = $monevService->createEvaluation($request->validated(), $request->user());

        return redirect()
            ->route('admin.monev.show', $evaluation->id)
            ->with('success', "Laporan Monev [{$evaluation->period_label}] program berhasil disimpan.");
    }

    /**
     * Display the specified evaluation report.
     */
    public function show(ProgramEvaluation $monev): View
    {
        Gate::authorize('view', $monev);

        $monev->load(['program.pic', 'program.creator', 'evaluator']);

        return view('admin.monev.show', ['evaluation' => $monev]);
    }

    /**
     * Show the form for editing the evaluation report.
     */
    public function edit(ProgramEvaluation $monev): View
    {
        Gate::authorize('update', $monev);

        $monev->load(['program', 'evaluator']);
        $programs = Program::orderBy('name')->get();
        $evaluators = User::active()->orderBy('name')->get();

        return view('admin.monev.edit', ['evaluation' => $monev, 'programs' => $programs, 'evaluators' => $evaluators]);
    }

    /**
     * Update the specified evaluation report.
     */
    public function update(UpdateProgramEvaluationRequest $request, ProgramEvaluation $monev, ProgramEvaluationService $monevService): RedirectResponse
    {
        $monev = $monevService->updateEvaluation($monev, $request->validated(), $request->user());

        return redirect()
            ->route('admin.monev.show', $monev->id)
            ->with('success', "Laporan Monev [{$monev->period_label}] berhasil diperbarui.");
    }

    /**
     * Remove the specified evaluation report.
     */
    public function destroy(ProgramEvaluation $monev, ProgramEvaluationService $monevService): RedirectResponse
    {
        Gate::authorize('delete', $monev);

        $monevService->deleteEvaluation($monev, auth()->user());

        return redirect()
            ->route('admin.monev.index')
            ->with('success', 'Laporan Monev berhasil dihapus.');
    }
}
