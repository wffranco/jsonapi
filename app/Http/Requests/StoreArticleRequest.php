<?php

namespace App\Http\Requests;

use App\Models\Category;
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

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated('data', []);

        $slug = Arr::get($validated, 'relationships.category.data.id');
        if ($category = Category::where('slug', $slug)->first()) {
            Arr::set($validated, 'attributes.category_id', $category->id);
        }

        if ($author_id = Arr::get($validated, 'relationships.author.data.id')) {
            Arr::set($validated, 'attributes.user_id', $author_id);
        }

        return Arr::get($validated, $key, $default);
    }
}
