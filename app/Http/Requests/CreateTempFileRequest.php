<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTempFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $user = auth()->user();
        if ($user) {
            $metadata = $this->input('metadata', []);
            if (!isset($metadata['college_id']) && $user->college_id) {
                $metadata['college_id'] = $user->college_id;
            }
            if (!isset($metadata['program_id']) && $user->program_id) {
                $metadata['program_id'] = $user->program_id;
            }
            $this->merge([
                'metadata' => $metadata,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'metadata.title' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:1000',
            'metadata.area_id' => ['nullable', 'exists:areas,id'],
            'metadata.is_general' => ['nullable', 'boolean'],
            'filename' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['pdf', 'mp4'])) {
                        $fail('File format is not supported. (Supported files: PDF or MP4)');
                    }
                }
            ],
        ];

        if ($user) {
            if ($user->hasRole('taskforce')) {
                $rules['metadata.college_id'] = ['required', 'exists:colleges,id'];
                $rules['metadata.program_id'] = ['required', 'exists:programs,id'];
            } elseif ($user->hasRole('college_officer')) {
                $rules['metadata.college_id'] = ['required', 'exists:colleges,id'];
                $isGeneral = $this->has('metadata.is_general') 
                    ? filter_var($this->input('metadata.is_general'), FILTER_VALIDATE_BOOLEAN) 
                    : true;
                $rules['metadata.program_id'] = [$isGeneral ? 'nullable' : 'required', 'exists:programs,id'];
            } else {
                $rules['metadata.college_id'] = ['nullable', 'exists:colleges,id'];
                $rules['metadata.program_id'] = ['nullable', 'exists:programs,id'];
            }
        } else {
            $rules['metadata.college_id'] = ['nullable', 'exists:colleges,id'];
            $rules['metadata.program_id'] = ['nullable', 'exists:programs,id'];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'metadata.college_id.required' => 'A college must be assigned to your account to upload files.',
            'metadata.program_id.required' => 'A program must be assigned to your account to upload files.',
        ];
    }
}
