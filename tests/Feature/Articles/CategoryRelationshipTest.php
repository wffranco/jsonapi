<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CategoryResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_category_identifier()
    {
        $article = Article::factory()->createOne();

        $this->getJsonApi(route('api.v1.articles.relationships.category', $article))
            ->assertExactJson(CategoryResource::getIdentifier($article->category));
    }

    public function test_can_fetch_the_associated_category_resource()
    {
        $article = Article::factory()->createOne();

        $this->getJsonApi(route('api.v1.articles.category', $article))
            ->assertExactJson(CategoryResource::getResource($article->category));
    }

    public function test_can_update_the_associated_category()
    {
        $article = Article::factory()->createOne();
        $category = Category::factory()->createOne();

        $this->actingAs($article->author)
            ->patchJsonApi(
                route('api.v1.articles.relationships.category', $article),
                CategoryResource::getIdentifier($category),
            )
            ->assertOk()
            ->assertExactJson(CategoryResource::getResource($category));

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_category_must_exists_in_database_when_updating()
    {
        $article = Article::factory()->createOne();

        $this->actingAs($article->author)
            ->patchJsonApi(
                route('api.v1.articles.relationships.category', $article),
                ['data' => ['type' => 'categories', 'id' => 999]],
            )
            ->assertJsonApiValidationErrors('data.id');
    }
}
