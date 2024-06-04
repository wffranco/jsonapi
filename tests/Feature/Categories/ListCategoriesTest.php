<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\CategoryController
 */
class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_category(): void
    {
        $category = Category::factory()->create();

        $this->getJsonApi(route('api.v1.categories.show', $category))
            ->assertOk()
            ->assertJsonApiResource($category, ['name', 'slug']);
    }

    public function test_can_fetch_all_categories(): void
    {
        $categories = Category::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.categories.index'))
            ->assertOk()
            ->assertJsonApiCollection($categories, ['name', 'slug']);
    }
}
