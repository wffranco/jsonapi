<?php

namespace Tests\Feature;

use App\JsonApi\Http\Middleware\ValidateHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::prefix('/test/api')->middleware(ValidateHeaders::class)->group(function () {
            Route::any('headers', fn () => 'OK');
            Route::any('empty', fn () => response()->noContent());
        });
    }

    public function test_accept_header_must_be_present_in_all_requests(): void
    {
        $this->get('/test/api/headers')->assertStatus(406);

        $this->get('/test/api/headers', [
            'Accept' => 'application/vnd.api+json',
        ])->assertSuccessful();

        $this->getJsonApi('/test/api/headers')->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_post_requests(): void
    {
        $this->post('/test/api/headers', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->post('/test/api/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();

        $this->postJsonApi('/test/api/headers')->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_patch_requests(): void
    {
        $this->patch('/test/api/headers', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->patch('/test/api/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();

        $this->patchJsonApi('/test/api/headers')->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_responses(): void
    {
        $this->getJsonApi('/test/api/headers')->assertHeader('Content-Type', 'application/vnd.api+json');
        $this->postJsonApi('/test/api/headers')->assertHeader('Content-Type', 'application/vnd.api+json');
        $this->patchJsonApi('/test/api/headers')->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function test_content_type_header_must_not_be_present_in_empty_responses(): void
    {
        $this->getJsonApi('/test/api/empty')->assertHeaderMissing('Content-Type');
        $this->postJsonApi('/test/api/empty')->assertHeaderMissing('Content-Type');
        $this->patchJsonApi('/test/api/empty')->assertHeaderMissing('Content-Type');
        $this->deleteJsonApi('/test/api/empty')->assertHeaderMissing('Content-Type');
    }
}
