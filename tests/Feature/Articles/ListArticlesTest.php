<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_article(): void
    {
        $article = Article::factory()->create();

        $this->getJsonApi(route('api.v1.articles.show', $article))
            ->assertOk()
            ->assertJsonApiResource($article, ['title', 'content', 'slug']);
    }

    public function test_can_fetch_all_articles(): void
    {
        $articles = Article::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.articles.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonApiCollection($articles, ['title', 'content', 'slug']);
    }
}
