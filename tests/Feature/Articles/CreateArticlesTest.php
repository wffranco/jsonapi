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
        $attributes = [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'slug',
        ];
        $response = $this->postJsonApi(route('api.v1.articles.store'), $attributes);

        $article = Article::first();
        $route = route('api.v1.articles.show', $article);
        $response->assertCreated()
            ->assertHeader('Location', $route)
            ->assertExactJson([
                'data' => [
                    'type' => 'articles',
                    'id' => (string) $article->getRouteKey(),
                    'attributes' => $attributes,
                    'links' => ['self' => $route],
                ],
            ]);
    }

    public function test_title_is_required(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'content' => 'Content',
            'slug' => 'slug',
        ])
            ->assertJsonApiValidationErrors('title');
    }

    public function test_content_is_required(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'slug' => 'slug',
        ])
            ->assertJsonApiValidationErrors('content');
    }
}
