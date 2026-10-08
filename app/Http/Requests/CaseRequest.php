<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Used for BOTH create and update (web + API). */
class CaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization runs BEFORE validation, so a Viewer always gets 403, never 422.
        $case = $this->route('case');

        return $case instanceof MedicalCase
            ? $this->user()->can('update', $case)
            : $this->user()->can('create', MedicalCase::class);
    }

    public function rules(): array
    {
        $case = $this->route('case'); // null on create, the model on update

        return [
            'case_number' => [
                'required', 'string', 'max:50',
                Rule::unique('cases', 'case_number')->ignore($case instanceof MedicalCase ? $case->id : null),
            ],
            'patient_reference' => ['required', 'string', 'max:100'],
            'surgeon_name' => ['required', 'string', 'max:150'],
            'implant_type' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(MedicalCase::STATUSES)],
            'priority' => ['required', Rule::in(MedicalCase::PRIORITIES)],
            'surgery_date' => ['nullable', 'date'],
        ];
    }
}
