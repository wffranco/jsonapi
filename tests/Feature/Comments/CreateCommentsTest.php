<?php

namespace Tests\Feature\Comments;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_comment()
    {
        $this->postJsonApi(route('api.v1.comments.store'), [
            'body' => 'This is a comment',
        ])
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: 401,
            );

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_user_can_create_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $this->actingAs($user, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => 'This is a comment',
                ],
                'relationships' => [
                    'article' => $article,
                    'author' => $user,
                ],
            ])
            ->assertCreated()
            ->assertJsonApiResource($comment = Comment::first(), ['body']);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_body_is_required()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();
        $this->actingAs($user, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => null,
                ],
                'relationships' => [
                    'article' => $article,
                    'author' => $user,
                ],
            ])
            ->assertJsonApiValidationErrors('body');
    }

    public function test_article_relationship_is_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => 'This is a comment',
                ],
                'relationships' => [
                    'author' => $user,
                ],
            ])
            ->assertJsonApiValidationErrors('relationships.article');
    }

    public function test_article_relationship_must_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => 'This is a comment',
                ],
                'relationships' => [
                    'article.data.id' => 'non-existing-article-slug',
                    'author' => $user,
                ],
            ])
            ->assertJsonApiValidationErrors('relationships.article');
    }

    public function test_author_relationship_is_required()
    {
        $article = Article::factory()->create();
        $this->actingAs(null, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => 'This is a comment',
                ],
                'relationships' => [
                    'article' => $article,
                ],
            ])
            ->assertJsonApiValidationErrors('relationships.author');
    }

    public function test_author_relationship_must_exist()
    {
        $article = Article::factory()->create();
        $this->actingAs(null, ['comment:create'])
            ->postJsonApi(route('api.v1.comments.store'), [
                'attributes' => [
                    'body' => 'This is a comment',
                ],
                'relationships' => [
                    'article' => $article,
                    'author.data.id' => 999,
                ],
            ])
            ->assertJsonApiValidationErrors('relationships.author');
    }
}
