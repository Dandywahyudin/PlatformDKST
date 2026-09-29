<?php

namespace App\Http\Requests;

use App\Models\ProgramEvaluation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ProgramEvaluation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'evaluator_id' => ['nullable', 'exists:users,id'],
            'evaluation_period' => [
                'required',
                'string',
                Rule::in([
                    ProgramEvaluation::PERIOD_Q1,
                    ProgramEvaluation::PERIOD_Q2,
                    ProgramEvaluation::PERIOD_Q3,
                    ProgramEvaluation::PERIOD_Q4,
                    ProgramEvaluation::PERIOD_MIDTERM,
                    ProgramEvaluation::PERIOD_FINAL,
                    ProgramEvaluation::PERIOD_MONTHLY,
                ]),
            ],
            'evaluation_date' => ['required', 'date'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'budget_realization' => ['required', 'numeric', 'min:0'],
            'achievements' => ['nullable', 'string', 'max:5000'],
            'obstacles' => ['nullable', 'string', 'max:5000'],
            'recommendations' => ['nullable', 'string', 'max:5000'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    ProgramEvaluation::STATUS_DRAFT,
                    ProgramEvaluation::STATUS_SUBMITTED,
                    ProgramEvaluation::STATUS_REVIEWED,
                    ProgramEvaluation::STATUS_APPROVED,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => 'Program yang dievaluasi wajib dipilih.',
            'evaluation_period.required' => 'Periode evaluasi wajib dipilih.',
            'evaluation_date.required' => 'Tanggal evaluasi wajib diisi.',
            'progress_percentage.required' => 'Persentase progres capaian wajib diisi (0-100%).',
            'score.required' => 'Skor evaluasi monev wajib diisi (0-100).',
        ];
    }
}
