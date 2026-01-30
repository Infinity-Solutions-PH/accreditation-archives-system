<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'college' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
            'expiration' => ['required', 'date'],

            'file' => [
                'required',
                'file',
                'mimes:pdf,docx,xlsx',
                'max:51200', // 50MB
            ],
        ];
    }
}
