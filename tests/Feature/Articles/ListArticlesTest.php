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

        $this->getJson(route('api.v1.articles.show', $article))
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
                        'self' => route('api.v1.articles.show', $article),
                    ],
                ],
            ]);
    }
}
