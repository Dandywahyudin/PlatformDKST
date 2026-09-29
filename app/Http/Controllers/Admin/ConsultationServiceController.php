<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationServiceRequest;
use App\Http\Requests\UpdateConsultationServiceRequest;
use App\Models\ConsultationService;
use App\Models\User;
use App\Services\ConsultationServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConsultationServiceController extends Controller
{
    /**
     * Display a listing of consultation service requests.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ConsultationService::class);

        $query = ConsultationService::with(['applicant', 'consultant'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->input('service_type'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhereHas('applicant', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $services = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => ConsultationService::count(),
            'pending' => ConsultationService::where('status', ConsultationService::STATUS_PENDING)->count(),
            'scheduled' => ConsultationService::where('status', ConsultationService::STATUS_SCHEDULED)->count(),
            'completed' => ConsultationService::where('status', ConsultationService::STATUS_COMPLETED)->count(),
            'in_review' => ConsultationService::where('status', ConsultationService::STATUS_IN_REVIEW)->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    /**
     * Show the form for creating a new consultation request.
     */
    public function create(): View
    {
        Gate::authorize('create', ConsultationService::class);

        $applicants = User::active()->orderBy('name')->get();
        $consultants = User::active()->orderBy('name')->get();

        return view('admin.services.create', compact('applicants', 'consultants'));
    }

    /**
     * Store a newly created consultation request.
     */
    public function store(StoreConsultationServiceRequest $request, ConsultationServiceService $serviceService): RedirectResponse
    {
        $service = $serviceService->createService($request->validated(), $request->user());

        return redirect()
            ->route('admin.services.show', $service->id)
            ->with('success', "Permohonan Layanan & Konsultasi [{$service->ticket_number}] berhasil didaftarkan.");
    }

    /**
     * Display the specified consultation request.
     */
    public function show(ConsultationService $service): View
    {
        Gate::authorize('view', $service);

        $service->load(['applicant', 'consultant']);
        $consultants = User::active()->orderBy('name')->get();

        return view('admin.services.show', compact('service', 'consultants'));
    }

    /**
     * Show the form for editing the consultation request.
     */
    public function edit(ConsultationService $service): View
    {
        Gate::authorize('update', $service);

        $service->load(['applicant', 'consultant']);
        $applicants = User::active()->orderBy('name')->get();
        $consultants = User::active()->orderBy('name')->get();

        return view('admin.services.edit', compact('service', 'applicants', 'consultants'));
    }

    /**
     * Update the specified consultation request.
     */
    public function update(UpdateConsultationServiceRequest $request, ConsultationService $service, ConsultationServiceService $serviceService): RedirectResponse
    {
        $service = $serviceService->updateService($service, $request->validated(), $request->user());

        return redirect()
            ->route('admin.services.show', $service->id)
            ->with('success', "Permohonan [{$service->ticket_number}] berhasil diperbarui.");
    }

    /**
     * Schedule a consultation meeting.
     */
    public function schedule(Request $request, ConsultationService $service, ConsultationServiceService $serviceService): RedirectResponse
    {
        Gate::authorize('update', $service);

        $validated = $request->validate([
            'consultant_id' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'meeting_link_or_location' => ['required', 'string', 'max:255'],
        ]);

        $serviceService->scheduleService($service, $validated, $request->user());

        return redirect()
            ->route('admin.services.show', $service->id)
            ->with('success', "Sesi konsultasi [{$service->ticket_number}] berhasil dijadwalkan.");
    }

    /**
     * Complete a consultation meeting.
     */
    public function complete(Request $request, ConsultationService $service, ConsultationServiceService $serviceService): RedirectResponse
    {
        Gate::authorize('update', $service);

        $validated = $request->validate([
            'consultation_notes' => ['required', 'string', 'min:5', 'max:5000'],
            'action_plan' => ['nullable', 'string', 'max:5000'],
        ]);

        $serviceService->completeService($service, $validated, $request->user());

        return redirect()
            ->route('admin.services.show', $service->id)
            ->with('success', "Sesi konsultasi [{$service->ticket_number}] berhasil diselesaikan dengan catatan rekomendasi.");
    }

    /**
     * Reject a consultation request.
     */
    public function reject(Request $request, ConsultationService $service, ConsultationServiceService $serviceService): RedirectResponse
    {
        Gate::authorize('update', $service);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $serviceService->rejectService($service, $validated['rejection_reason'], $request->user());

        return redirect()
            ->route('admin.services.show', $service->id)
            ->with('success', "Permohonan konsultasi [{$service->ticket_number}] telah ditolak dengan catatan alasan.");
    }

    /**
     * Remove the specified consultation request.
     */
    public function destroy(ConsultationService $service, ConsultationServiceService $serviceService): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $ticket = $service->ticket_number;
        $serviceService->deleteService($service, auth()->user());

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Permohonan Layanan [{$ticket}] berhasil dihapus.");
    }
}
