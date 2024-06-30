<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\ArticleController
 */
class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_articles(): void
    {
        $this->postJsonApi(route('api.v1.articles.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: 401,
            );
        $this->assertDatabaseCount('articles', 0);
    }

    public function test_can_create_articles(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'attributes' => [
                    'title' => 'Title',
                    'content' => 'Content',
                    'slug' => 'slug',
                ],
                'relationships' => [
                    'author' => $user,
                    'category' => $category,
                ],
            ])
            ->assertCreated()
            ->assertJsonApiResource($article = Article::first(), ['title', 'content', 'slug']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_title_is_required(): void
    {

        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'content' => 'Content',
                'slug' => 'slug',
            ])
            ->assertJsonApiValidationErrors('title');
    }

    public function test_content_is_required(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'slug' => 'slug',
            ])
            ->assertJsonApiValidationErrors('content');
    }

    public function test_article_category_relationship_is_required(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'slug',
            ])
            ->assertJsonApiValidationErrors('relationships.category');
    }

    public function test_article_category_must_exist(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'attributes' => [
                    'title' => 'Title',
                    'content' => 'Content',
                    'slug' => 'slug',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'any-id',
                        ],
                    ],
                ],
            ])
            ->assertJsonApiValidationErrors('relationships.category');
    }

    public function test_slug_is_unique_on_create(): void
    {
        $article = Article::factory()->create();

        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => $article->slug,
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_be_a_string(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 1,
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_start_with_a_hyphen(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => '-slug',
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_end_with_a_hyphen(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'slug-',
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_consecutive_hyphens(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'slug--slug',
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_invalid_characters(): void
    {
        $this->actingAs(null, ['article:create'])
            ->postJsonApi(route('api.v1.articles.store'), [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'Invalid Slug',
            ])
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_match_a_valid_slug_pattern(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, ['article:create']);

        $category = Category::factory()->create();

        $this->postJsonApi(route('api.v1.articles.store'), [
            'attributes' => [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => 'valid-slug-01',
            ],
            'relationships' => [
                'author' => $user,
                'category' => $category,
            ],
        ])
            ->assertCreated();

        $this->postJsonApi(route('api.v1.articles.store'), [
            'attributes' => [
                'title' => 'Title',
                'content' => 'Content',
                'slug' => \Str::slug('-Make-a valid--slug from invalid^-'),
            ],
            'relationships' => [
                'author' => $user,
                'category' => $category,
            ],
        ])
            ->assertCreated();
    }
}
