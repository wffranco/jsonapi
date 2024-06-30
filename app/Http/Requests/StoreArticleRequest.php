<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
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
            'data.type' => ['required', 'in:articles'],
            'data.id' => [
                Rule::requiredIf($this->route('article')),
                Rule::exists('articles', 'slug'),
            ],
            'data.attributes.title' => ['required'],
            'data.attributes.content' => ['required', 'string'],
            'data.attributes.slug' => [
                'required',
                new Slug,
                Rule::unique('articles', 'slug')->ignore($this->route('article')),
            ],
            'data.relationships.author.data.id' => [
                Rule::requiredIf(! $this->route('article')),
                Rule::exists('users', 'id'),
            ],
            'data.relationships.category.data.id' => [
                Rule::requiredIf(! $this->route('article')),
                Rule::exists('categories', 'slug'),
            ],
        ];
    }
}
