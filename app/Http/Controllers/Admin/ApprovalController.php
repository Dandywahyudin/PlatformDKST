<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Display a listing of approval requests.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Approval::class);

        $query = Approval::with(['program.pic', 'requester', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('program', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('requester', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $approvals = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Approval::count(),
            'pending' => Approval::where('status', Approval::STATUS_PENDING)->count(),
            'approved' => Approval::where('status', Approval::STATUS_APPROVED)->count(),
            'rejected' => Approval::where('status', Approval::STATUS_REJECTED)->count(),
        ];

        return view('admin.approvals.index', compact('approvals', 'stats'));
    }

    /**
     * Display the specified approval request.
     */
    public function show(Approval $approval): View
    {
        Gate::authorize('view', $approval);

        $approval->load([
            'program.pic',
            'program.creator',
            'program.members',
            'program.documents',
            'requester',
            'reviewer',
            'actions.user',
        ]);

        return view('admin.approvals.show', compact('approval'));
    }

    /**
     * Approve the program proposal.
     */
    public function approve(Request $request, Approval $approval, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize('process', $approval);

        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $approvalService->approveProgram($approval, $request->user(), $validated['comment'] ?? null);

        $redirectRoute = $request->routeIs('director.*') ? 'director.approvals.show' : 'admin.approvals.show';

        return redirect()
            ->route($redirectRoute, $approval->id)
            ->with('success', "Usulan program [{$approval->program?->code}] berhasil disetujui (Approved).");
    }

    /**
     * Reject the program proposal.
     */
    public function reject(Request $request, Approval $approval, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize('process', $approval);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'reason.required' => 'Alasan penolakan usulan program wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $approvalService->rejectProgram($approval, $request->user(), $validated['reason']);

        $redirectRoute = $request->routeIs('director.*') ? 'director.approvals.show' : 'admin.approvals.show';

        return redirect()
            ->route($redirectRoute, $approval->id)
            ->with('success', "Usulan program [{$approval->program?->code}] telah ditolak dengan catatan perbaikan.");
    }
}
