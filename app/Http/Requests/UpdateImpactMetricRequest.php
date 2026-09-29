<?php

namespace App\Http\Requests;

use App\Models\ImpactMetric;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImpactMetricRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage', ImpactMetric::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2020', 'max:2035'],
            'category' => [
                'required',
                'string',
                Rule::in([
                    ImpactMetric::CAT_STARTUP_GROWTH,
                    ImpactMetric::CAT_PATENT_HKI,
                    ImpactMetric::CAT_COMMERCIALIZATION,
                    ImpactMetric::CAT_WORKFORCE,
                    ImpactMetric::CAT_FUNDING_INVESTMENT,
                    ImpactMetric::CAT_SOCIO_ECONOMIC,
                ]),
            ],
            'metric_name' => ['required', 'string', 'min:3', 'max:255'],
            'target_value' => ['required', 'numeric', 'min:0'],
            'realized_value' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
