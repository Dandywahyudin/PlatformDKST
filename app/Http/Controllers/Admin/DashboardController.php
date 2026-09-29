<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Program;
use App\Models\Task;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $kpis = [
            'total_users' => User::count(),
            'total_programs' => Program::count(),
            'active_programs' => Program::whereIn('status', [
                Program::STATUS_IN_PROGRESS,
                Program::STATUS_APPROVED,
            ])->count(),
            'pending_approvals' => Approval::where('status', Approval::STATUS_PENDING)->count(),
            'pending_tasks' => Task::whereIn('status', [
                Task::STATUS_TODO,
                Task::STATUS_IN_PROGRESS,
            ])->count(),
            'total_documents' => Document::count(),
        ];

        $programStatusCounts = [
            'DRAFT' => Program::where('status', Program::STATUS_DRAFT)->count(),
            'SUBMITTED' => Program::where('status', Program::STATUS_SUBMITTED)->count(),
            'UNDER_REVIEW' => Program::where('status', Program::STATUS_UNDER_REVIEW)->count(),
            'APPROVED' => Program::where('status', Program::STATUS_APPROVED)->count(),
            'REJECTED' => Program::where('status', Program::STATUS_REJECTED)->count(),
            'IN_PROGRESS' => Program::where('status', Program::STATUS_IN_PROGRESS)->count(),
            'COMPLETED' => Program::where('status', Program::STATUS_COMPLETED)->count(),
        ];

        $recentPrograms = Program::with(['pic', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        $pendingApprovals = Approval::with(['program', 'requester'])
            ->where('status', Approval::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $recentDocuments = Document::with(['uploader', 'program'])
            ->latest()
            ->take(5)
            ->get();

        $recentTasks = Task::with(['assignee', 'program'])
            ->whereIn('status', [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'kpis',
            'programStatusCounts',
            'recentPrograms',
            'pendingApprovals',
            'recentActivities',
            'recentDocuments',
            'recentTasks'
        ));
    }
}
