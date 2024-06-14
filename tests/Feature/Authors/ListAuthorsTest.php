<?php

namespace Tests\Feature\Authors;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\AuthorController
 */
class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_a_single_author()
    {
        $author = User::factory()->create();

        $this->getJsonApi(route('api.v1.authors.show', $author))
            ->assertOk()
            ->assertJsonApiResource($author, ['alias', 'name', 'email']);
    }

    public function test_fetch_all_authors()
    {
        $authors = User::factory()->count(3)->create();

        $this->getJsonApi(route('api.v1.authors.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonApiCollection($authors, ['alias', 'name', 'email']);
    }
}
