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
        $response = $this->postJsonApi(route('api.v1.articles.store'), ['data' => $data]);

        $article = Article::first();
        $route = route('api.v1.articles.show', $article);
        $response->assertCreated()
            ->assertHeader('Location', $route)
            ->assertExactJson([
                'data' => array_merge(
                    ['id' => (string) $article->getRouteKey()],
                    $data,
                    ['links' => ['self' => $route]],
                ),
            ]);
    }

    public function test_title_is_required(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'content' => 'Content',
                    'slug' => 'slug',
                ],
            ],
        ])
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertJsonStructure([
                'errors' => [
                    ['title', 'detail', 'source' => ['pointer']],
                ],
            ])
            ->assertJsonFragment([
                'source' => ['pointer' => '/data/attributes/title'],
            ]);
    }

    public function test_content_is_required(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Title',
                    'slug' => 'slug',
                ],
            ],
        ])
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertJsonStructure([
                'errors' => [
                    ['title', 'detail', 'source' => ['pointer']],
                ],
            ])
            ->assertJsonFragment([
                'source' => ['pointer' => '/data/attributes/content'],
            ]);
    }
}
