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

    public function test_slug_is_unique_on_create(): void
    {
        $article = Article::factory()->create();

        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => $article->slug,
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_be_a_string(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 1,
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_start_with_a_hyphen(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => '-slug',
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_end_with_a_hyphen(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'slug-',
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_consecutive_hyphens(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'slug--slug',
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_invalid_characters(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'Invalid Slug',
        ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_match_a_valid_slug_pattern(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'valid-slug-01',
        ])->assertCreated();
        $this->postJsonApi(route('api.v1.articles.store'), [
            'title' => 'Title',
            'content' => 'Content',
            'slug' => \Str::slug('-Make-a valid--slug from invalid^-'),
        ])->assertCreated();
    }
}
