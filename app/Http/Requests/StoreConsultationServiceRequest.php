<?php

namespace App\Http\Requests;

use App\Models\ConsultationService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ConsultationService::class);
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
            'scheduled_at' => ['nullable', 'date'],
            'meeting_link_or_location' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'service_type.required' => 'Jenis layanan & konsultasi wajib dipilih.',
            'title.required' => 'Judul permohonan / topik konsultasi wajib diisi.',
            'title.min' => 'Judul permohonan minimal 5 karakter.',
            'description.required' => 'Deskripsi kebutuhan konsultasi wajib diisi.',
            'description.min' => 'Deskripsi kebutuhan minimal 10 karakter.',
        ];
    }
}
