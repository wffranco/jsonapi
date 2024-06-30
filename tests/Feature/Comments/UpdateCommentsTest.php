<?php

namespace Tests\Feature\Comments;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_update_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->patchJsonApi(route('api.v1.comments.update', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: 401,
            );
    }

    public function test_can_update_owned_comments(): void
    {
        $comment = Comment::factory()->create();
        $this->actingAs($comment->author, ['comment:update'])
            ->patchJsonApi(route('api.v1.comments.update', $comment), [
                'body' => $body = 'Body',
            ])
            ->assertOk()
            ->assertJsonApiResource($comment->refresh(), ['body']);

        $this->assertEquals($comment->body, $body);
    }

    public function test_cannot_update_other_users_comments(): void
    {
        $comment = Comment::factory()->create();
        $this->actingAs(null, ['comment:update'])
            ->patchJsonApi(route('api.v1.comments.update', $comment), [
                'body' => $body = 'Body',
            ])
            ->assertForbidden();

        $this->assertNotEquals($comment->refresh()->body, $body);
    }

    public function test_body_is_required(): void
    {
        $comment = Comment::factory()->create();
        $this->actingAs($comment->author, ['comment:update'])
            ->patchJsonApi(route('api.v1.comments.update', $comment), [
                'body' => null,
            ])
            ->assertJsonApiValidationErrors('body');

        $this->assertNotEquals($comment->refresh()->body, null);
    }

    public function test_can_update_owned_comments_with_relationships(): void
    {
        $article = Article::factory()->create();
        $comment = Comment::factory()->create();
        $this->actingAs($comment->author, ['comment:update'])
            ->patchJsonApi(route('api.v1.comments.update', $comment), [
                'attributes' => [
                    'body' => 'Body',
                ],
                'relationships' => [
                    'article' => $article,
                    'author' => $comment->author,
                ],
            ])
            ->assertOk()
            ->assertJsonApiResource($comment->refresh(), ['body']);

        $this->assertTrue($article->is($comment->article));
    }
}
