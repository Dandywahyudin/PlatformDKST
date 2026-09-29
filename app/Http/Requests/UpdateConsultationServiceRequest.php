<?php

namespace App\Http\Requests;

use App\Models\ConsultationService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultationServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('service'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_type' => [
                'required',
                'string',
                Rule::in([
                    ConsultationService::TYPE_VALUASI_TEKNOLOGI,
                    ConsultationService::TYPE_FASILITASI_HKI,
                    ConsultationService::TYPE_INKUBASI_STARTUP,
                    ConsultationService::TYPE_HILIRISASI_INDUSTRI,
                    ConsultationService::TYPE_LEGALITAS_KONTRAK,
                    ConsultationService::TYPE_LAINNYA,
                ]),
            ],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'institution' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'applicant_id' => ['nullable', 'exists:users,id'],
            'consultant_id' => ['nullable', 'exists:users,id'],
            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    ConsultationService::STATUS_PENDING,
                    ConsultationService::STATUS_IN_REVIEW,
                    ConsultationService::STATUS_SCHEDULED,
                    ConsultationService::STATUS_COMPLETED,
                    ConsultationService::STATUS_REJECTED,
                    ConsultationService::STATUS_CANCELLED,
                ]),
            ],
            'scheduled_at' => ['nullable', 'date'],
            'meeting_link_or_location' => ['nullable', 'string', 'max:255'],
            'consultation_notes' => ['nullable', 'string', 'max:5000'],
            'action_plan' => ['nullable', 'string', 'max:5000'],
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
