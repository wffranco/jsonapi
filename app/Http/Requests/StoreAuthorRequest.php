<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.type' => ['required', 'in:authors'],
            'data.attributes.alias' => ['required', 'string', 'unique:users,alias'],
            'data.attributes.name' => ['required', 'string'],
            'data.attributes.email' => ['required', 'email', 'unique:users,email'],
            'data.attributes.password' => ['required', 'string', 'min:8'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated('data');

        return data_get($validated, $key, $default);
    }
}
