<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ConsultationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConsultationServiceService
{
    /**
     * Generate unique sequential ticket number: DKST-SRV-YYYY-XXXX.
     */
    public function generateTicketNumber(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = "DKST-SRV-{$year}-";

        $lastRecord = ConsultationService::withTrashed()
            ->where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('ticket_number', 'desc')
            ->first();

        if (! $lastRecord) {
            return $prefix.'0001';
        }

        $lastSequence = (int) substr($lastRecord->ticket_number, -4);
        $newSequence = $lastSequence + 1;

        return $prefix.str_pad((string) $newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new consultation request.
     */
    public function createService(array $data, User $applicant): ConsultationService
    {
        return DB::transaction(function () use ($data, $applicant) {
            $data['ticket_number'] = $this->generateTicketNumber();
            $data['applicant_id'] = $data['applicant_id'] ?? $applicant->id;
            $data['status'] = $data['status'] ?? ConsultationService::STATUS_PENDING;

            $service = ConsultationService::create($data);

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_CREATE,
                "Mengajukan permohonan layanan & konsultasi: [{$service->ticket_number}] {$service->title}",
                $service,
                null,
                $service->toArray()
            );

            return $service;
        });
    }

    /**
     * Update an existing consultation request.
     */
    public function updateService(ConsultationService $service, array $data, User $user): ConsultationService
    {
        return DB::transaction(function () use ($service, $data) {
            $oldValues = $service->toArray();
            $service->update($data);

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_UPDATE,
                "Memperbarui layanan & konsultasi: [{$service->ticket_number}] {$service->title}",
                $service,
                $oldValues,
                $service->fresh()->toArray()
            );

            return $service;
        });
    }

    /**
     * Schedule a consultation session.
     */
    public function scheduleService(ConsultationService $service, array $data, User $user): ConsultationService
    {
        return DB::transaction(function () use ($service, $data) {
            $oldValues = $service->toArray();

            $service->update([
                'consultant_id' => $data['consultant_id'] ?? $service->consultant_id,
                'scheduled_at' => $data['scheduled_at'] ?? $service->scheduled_at,
                'meeting_link_or_location' => $data['meeting_link_or_location'] ?? $service->meeting_link_or_location,
                'status' => ConsultationService::STATUS_SCHEDULED,
            ]);

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_UPDATE,
                "Menjadwalkan sesi konsultasi: [{$service->ticket_number}] {$service->title}",
                $service,
                $oldValues,
                $service->fresh()->toArray()
            );

            return $service;
        });
    }

    /**
     * Complete a consultation session.
     */
    public function completeService(ConsultationService $service, array $data, User $user): ConsultationService
    {
        return DB::transaction(function () use ($service, $data) {
            $oldValues = $service->toArray();

            $service->update([
                'status' => ConsultationService::STATUS_COMPLETED,
                'consultation_notes' => $data['consultation_notes'] ?? $service->consultation_notes,
                'action_plan' => $data['action_plan'] ?? $service->action_plan,
            ]);

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_UPDATE,
                "Menyelesaikan sesi konsultasi: [{$service->ticket_number}] {$service->title}",
                $service,
                $oldValues,
                $service->fresh()->toArray()
            );

            return $service;
        });
    }

    /**
     * Reject a consultation request.
     */
    public function rejectService(ConsultationService $service, string $reason, User $user): ConsultationService
    {
        return DB::transaction(function () use ($service, $reason) {
            $oldValues = $service->toArray();

            $service->update([
                'status' => ConsultationService::STATUS_REJECTED,
                'rejection_reason' => $reason,
            ]);

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_REJECT,
                "Menolak permohonan konsultasi: [{$service->ticket_number}] {$service->title}. Alasan: {$reason}",
                $service,
                $oldValues,
                $service->fresh()->toArray()
            );

            return $service;
        });
    }

    /**
     * Delete a consultation request.
     */
    public function deleteService(ConsultationService $service, User $user): void
    {
        DB::transaction(function () use ($service) {
            $ticketNumber = $service->ticket_number;
            $title = $service->title;

            $service->delete();

            AuditLogService::log(
                AuditLog::MODULE_SERVICES,
                AuditLog::ACTION_DELETE,
                "Menghapus permohonan layanan & konsultasi: [{$ticketNumber}] {$title}",
                $service,
                ['ticket_number' => $ticketNumber],
                null
            );
        });
    }
}
