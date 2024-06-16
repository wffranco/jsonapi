<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CategoryResource;
use App\Models\Article;
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
}
