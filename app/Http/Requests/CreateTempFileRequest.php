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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'metadata.title' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:1000',
            'metadata.college_id' => ['nullable', 'exists:colleges,id'],
            'metadata.program_id' => ['nullable', 'exists:programs,id'],
            'metadata.area_id' => ['nullable', 'exists:areas,id'],
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
    }
}
