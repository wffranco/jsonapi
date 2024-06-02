<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
            'data.attributes.title' => ['required'],
            'data.attributes.content' => ['required'],
            'data.attributes.slug' => [
                'required',
                new Slug,
                Rule::unique('articles', 'slug')->ignore($this->route('article')),
            ],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated('data', []);

        return Arr::get($validated, $key, $default);
    }
}
