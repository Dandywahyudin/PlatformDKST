<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Program::class);

        $query = Program::with(['pic', 'creator'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('pic_name', 'like', "%{$search}%")
                    ->orWhereHas('pic', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            $query->whereYear('start_date', $year);
        }

        $programs = $query->paginate(10)->withQueryString();
        $users = User::active()->orderBy('name')->get();

        $stats = [
            'total' => Program::count(),
            'draft' => Program::where('status', Program::STATUS_DRAFT)->count(),
            'submitted' => Program::where('status', Program::STATUS_SUBMITTED)->count(),
            'under_review' => Program::where('status', Program::STATUS_UNDER_REVIEW)->count(),
            'approved' => Program::where('status', Program::STATUS_APPROVED)->count(),
            'rejected' => Program::where('status', Program::STATUS_REJECTED)->count(),
            'in_progress' => Program::where('status', Program::STATUS_IN_PROGRESS)->count(),
            'completed' => Program::where('status', Program::STATUS_COMPLETED)->count(),
        ];

        return view('admin.programs.index', compact('programs', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create(): View
    {
        Gate::authorize('create', Program::class);

        $users = User::active()->orderBy('name')->get();

        return view('admin.programs.create', compact('users'));
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(StoreProgramRequest $request, ProgramService $programService): RedirectResponse
    {
        $program = $programService->createProgram($request->validated(), $request->user());

        return redirect()
            ->route('admin.programs.show', $program->id)
            ->with('success', "Usulan program [{$program->code}] {$program->name} berhasil dibuat (Draft).");
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program): View
    {
        Gate::authorize('view', $program);

        $program->load([
            'pic',
            'creator',
            'members',
            'documents.uploader',
            'tasks.assignee',
            'approvals.reviewer',
            'approvals.actions.user',
        ]);

        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program): View
    {
        Gate::authorize('update', $program);

        $program->load('members');
        $users = User::active()->orderBy('name')->get();
        $currentMemberIds = $program->members->pluck('id')->all();

        return view('admin.programs.edit', compact('program', 'users', 'currentMemberIds'));
    }

    /**
     * Update the specified program in storage.
     */
    public function update(UpdateProgramRequest $request, Program $program, ProgramService $programService): RedirectResponse
    {
        $program = $programService->updateProgram($program, $request->validated(), $request->user());

        return redirect()
            ->route('admin.programs.show', $program->id)
            ->with('success', "Program [{$program->code}] {$program->name} berhasil diperbarui.");
    }

    /**
     * Submit a program for approval review.
     */
    public function submit(Program $program, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize('submit', $program);

        $approval = $approvalService->submitProgram($program, auth()->user());

        return redirect()
            ->route('admin.programs.show', $program->id)
            ->with('success', "Usulan program [{$program->code}] berhasil disubmit ke antrean approval.");
    }

    /**
     * Start execution of an approved program.
     */
    public function start(Program $program, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize('update', $program);

        $approvalService->startProgram($program, auth()->user());

        return redirect()
            ->route('admin.programs.show', $program->id)
            ->with('success', "Program [{$program->code}] telah resmi dimulai (In Progress).");
    }

    /**
     * Mark an in-progress program as completed.
     */
    public function complete(Program $program, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize('update', $program);

        $approvalService->completeProgram($program, auth()->user());

        return redirect()
            ->route('admin.programs.show', $program->id)
            ->with('success', "Program [{$program->code}] telah ditandai selesai (Completed).");
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy(Program $program, ProgramService $programService): RedirectResponse
    {
        Gate::authorize('delete', $program);

        $programCode = $program->code;
        $programService->deleteProgram($program, auth()->user());

        return redirect()
            ->route('admin.programs.index')
            ->with('success', "Program [{$programCode}] berhasil dihapus.");
    }
}
