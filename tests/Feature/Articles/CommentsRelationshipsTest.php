<?php

namespace Tests\Feature\Articles;

use App\Http\Resources\CommentResource;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_comments_identifiers()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->hasComments(2)->createOne();
        $this->getJsonApi(route('api.v1.articles.relationships.comments', $article))
            ->assertJsonCount(2, 'data')
            ->assertExactJson(['data' => CommentResource::getIdentifiers($article->comments)]);
    }
}
