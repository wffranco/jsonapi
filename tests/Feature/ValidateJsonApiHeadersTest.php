<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any('/headers', fn () => 'OK')->middleware(ValidateJsonApiHeaders::class);
    }

    public function test_accept_header_must_be_present_in_all_requests(): void
    {
        $this->get('/headers')->assertStatus(406);

        $this->get('/headers', [
            'Accept' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_post_requests(): void
    {
        $this->post('/headers', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->post('/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_patch_requests(): void
    {
        Route::patch('/headers', fn () => 'OK')->middleware(ValidateJsonApiHeaders::class);

        $this->patch('/headers', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->patch('/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_responses(): void
    {
        $this->get('/headers', [
            'Accept' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
        $this->post('/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
        $this->patch('/headers', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function test_content_type_header_must_not_be_present_in_empty_responses(): void
    {
        Route::any('/empty', fn () => response()->noContent())->middleware(ValidateJsonApiHeaders::class);

        $this->get('/empty', [
            'Accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
        $this->post('/empty', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
        $this->patch('/empty', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
        $this->delete('/empty', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
    }
}
