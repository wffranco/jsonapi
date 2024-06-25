<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_delete_comments()
    {
        $comment = Comment::factory()->create();

        $this->assertDatabaseCount('comments', 1);

        $this->deleteJsonApi(route('api.v1.comments.destroy', $comment))
            ->assertUnauthorized();

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_can_delete_owned_comments()
    {
        $comment = Comment::factory()->create();

        $this->assertDatabaseCount('comments', 1);

        $this->actingAs($comment->author, ['comment:delete'])
            ->deleteJsonApi(route('api.v1.comments.destroy', $comment))
            ->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_other_users_comments()
    {
        $comment = Comment::factory()->create();

        $this->assertDatabaseCount('comments', 1);

        $this->actingAs(null, ['comment:delete'])
            ->deleteJsonApi(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
