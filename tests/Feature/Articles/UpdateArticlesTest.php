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

        $this->patchJsonApi(route('api.v1.articles.update', $article), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'slug',
        ])
            ->assertOk()
            ->assertJsonApiHeaderLocation($article->refresh())
            ->assertJsonApiResource($article, ['title', 'content', 'slug']);
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

    public function test_slug_is_unique_on_update(): void
    {
        $articles = Article::factory()->count(2)->create();

        // Slug can't be stored in another article
        $this->patchJsonApi(route('api.v1.articles.update', $articles[0]), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => $articles[1]->slug,
        ])
            ->assertJsonApiValidationErrors('slug');

        // Slug can be overridden by the same article
        $this->patchJsonApi(route('api.v1.articles.update', $articles[0]), [
            'title' => 'Title new',
            'content' => 'Content new',
            'slug' => $articles[0]->slug,
        ])
            ->assertOk();
    }
}
