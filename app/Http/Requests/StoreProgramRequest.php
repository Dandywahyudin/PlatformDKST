<?php

namespace App\Http\Requests;

use App\Models\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Program::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'pic_id' => ['nullable', 'exists:users,id'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['required', 'numeric', 'min:0'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'proposal_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'additional_files' => ['nullable', 'array'],
            'additional_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip', 'max:20480'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama usulan program wajib diisi.',
            'budget.required' => 'Besaran anggaran program wajib diisi.',
            'budget.numeric' => 'Anggaran harus berupa angka numerik valid.',
            'budget.min' => 'Anggaran program tidak boleh bernilai negatif.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'proposal_file.mimes' => 'Berkas proposal harus berformat PDF, DOC, atau DOCX.',
            'proposal_file.max' => 'Ukuran berkas proposal maksimal 20 MB.',
            'additional_files.*.mimes' => 'Berkas lampiran harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, atau ZIP.',
            'additional_files.*.max' => 'Ukuran berkas lampiran maksimal 20 MB per file.',
        ];
    }
}
