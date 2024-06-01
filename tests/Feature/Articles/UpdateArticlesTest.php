<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_articles(): void
    {
        $article = Article::factory()->create();
        $route = route('api.v1.articles.show', $article);

        $attributes = [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'slug',
        ];
        $this->patchJsonApi(route('api.v1.articles.update', $article), $attributes)
            ->assertOk()
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
        $article = Article::factory()->create();
        $this->patchJsonApi(route('api.v1.articles.update', $article), [
            'content' => 'Content',
            'slug' => 'slug',
        ])
            ->assertJsonApiValidationErrors('title');
    }

    public function test_content_is_required(): void
    {
        $article = Article::factory()->create();
        $this->patchJsonApi(route('api.v1.articles.update', $article), [
            'title' => 'Title',
            'slug' => 'slug',
        ])
            ->assertJsonApiValidationErrors('content');
    }
}
