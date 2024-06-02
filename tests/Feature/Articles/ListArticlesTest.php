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
        $route = route('api.v1.articles.show', $article);

        $this->getJsonApi($route)
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'type' => 'articles',
                    'id' => (string) $article->getRouteKey(),
                    'attributes' => [
                        'title' => $article->title,
                        'content' => $article->content,
                        'slug' => $article->slug,
                    ],
                    'links' => [
                        'self' => $route,
                    ],
                ],
            ]);
    }

    public function test_can_fetch_all_articles(): void
    {
        $articles = Article::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.articles.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'data' => $articles->map(fn (Article $article) => [
                    'type' => 'articles',
                    'id' => (string) $article->getRouteKey(),
                    'attributes' => [
                        'title' => $article->title,
                        'content' => $article->content,
                        'slug' => $article->slug,
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $article),
                    ],
                ])->all(),
                'links' => [
                    'self' => route('api.v1.articles.index'),
                ],
            ]);
    }
}
