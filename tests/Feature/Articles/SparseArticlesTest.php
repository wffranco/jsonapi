<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparseArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fields_can_be_sparsed_in_articles_index()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'fields' => ['articles' => 'title,slug'],
        ]))
            ->assertJsonApiCollectionStructure(['title', 'slug'])
            ->assertJsonApiCollection(Article::all(), ['title', 'slug'], ['content']);
    }

    public function test_route_key_is_always_included_in_articles_index()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'fields' => ['articles' => 'title'],
        ]))
            ->assertJsonApiCollection(Article::all(), ['title'], ['slug', 'content']);
    }

    public function test_cannot_sparse_fields_not_allowed_in_articles_index()
    {
        Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.index', [
            'fields' => ['articles' => 'title,slug,foo'],
        ]))->assertStatus(400);
    }

    public function test_fields_can_be_sparsed_in_articles_show()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'fields' => ['articles' => 'title,slug'],
        ]))
            ->assertJsonApiResource($article, ['title', 'slug'], ['content']);
    }

    public function test_cannot_sparse_fields_not_allowed_in_articles_show()
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.show', [
            'article' => $article,
            'fields' => ['articles' => 'title,slug,foo'],
        ]))->assertStatus(400);
    }
}
