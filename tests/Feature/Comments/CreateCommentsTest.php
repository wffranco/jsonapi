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
            ->assertJsonApiHeaderLocation($comment = Comment::first())
            ->assertJsonApiResource($comment, ['body']);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }
}
