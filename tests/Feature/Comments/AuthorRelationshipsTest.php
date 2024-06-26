<?php

namespace Tests\Feature\Comments;

use App\Http\Resources\AuthorResource;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_author_identifier()
    {
        $comment = Comment::factory()->createOne();

        $this->getJsonApi(route('api.v1.comments.relationships.author', $comment))
            ->assertExactJson(AuthorResource::getIdentifier($comment->author));
    }

    public function test_can_fetch_the_associated_author_resource()
    {
        $comment = Comment::factory()->createOne();

        $this->getJsonApi(route('api.v1.comments.author', $comment))
            ->assertExactJson(AuthorResource::getResource($comment->author));
    }

    public function test_can_update_the_associated_author()
    {
        $author = User::factory()->createOne();
        $comment = Comment::factory()->createOne();

        $this->actingAs($comment->author)
            ->patchJsonApi(
                route('api.v1.comments.relationships.author', $comment),
                AuthorResource::getIdentifier($author),
            )
            ->assertExactJson(AuthorResource::getResource($author))
            ->assertOk();

        $this->assertTrue($comment->refresh()->author->is($author));
    }

    public function test_author_must_exists_in_database_when_updating()
    {
        $comment = Comment::factory()->createOne();

        $this->actingAs($comment->author)
            ->patchJsonApi(
                route('api.v1.comments.relationships.author', $comment),
                ['type' => 'users', 'id' => '999'],
            )
            ->assertJsonApiValidationErrors('data.id');
    }
}
