<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_comment(): void
    {
        $comment = Comment::factory()->createOne();

        $this->getJsonApi(route('api.v1.comments.show', $comment))
            ->assertOk()
            ->assertJsonApiResource($comment, ['body'])
            ->assertJsonApiRelationshipLinks($comment, ['article', 'author']);
    }

    public function test_can_fetch_all_comments(): void
    {
        $comments = Comment::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.comments.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonApiCollection($comments, ['body']);
    }

    public function test_returns_a_json_api_error_when_comment_not_found(): void
    {
        $this->getJsonApi(route('api.v1.comments.show', ['comment' => 999]))
            ->assertNotFound()
            ->assertJsonApiError(
                title: 'Not Found',
                detail: "Not found the id '999' in the 'comments' resource.",
                status: 404,
            );
    }
}
