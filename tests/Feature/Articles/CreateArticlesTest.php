<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_articles(): void
    {
        $data = [
            'type' => 'articles',
            'attributes' => [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'slug',
            ],
        ];
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => $data,
        ]);

        $article = Article::first();
        $response->assertCreated()
            ->assertHeader('Location', route('api.v1.articles.show', $article))
            ->assertExactJson([
                'data' => $data + [
                    'id' => (string) $article->getRouteKey(),
                    'links' => [
                        'self' => route('api.v1.articles.show', $article),
                    ],
                ],
            ]);
    }
}
