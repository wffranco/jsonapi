<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_route_error_only_on_api_routes_and_json_api_requests()
    {
        // A `JsonApi` call to `api` routes should return a JSON:API error response.
        $this->getJsonApi('api/route')
            ->assertStatus(404)
            ->assertJsonApiError(
                title: 'Not Found',
                status: '404',
                detail: 'The route api/route could not be found.',
            );

        // A `Json` call to `api` routes should return a default laravel json response.
        $this->getJson('api/route')
            ->assertStatus(404)
            ->assertJson(['message' => 'The route api/route could not be found.']);

        // A normal call to `api` routes should return a default laravel html response.
        $this->get('api/route')
            ->assertStatus(404)
            ->assertSee('<title>Not Found</title>', false);

        // A `JsonApi` call to non `api` route should return a default laravel json response.
        $this->getJsonApi('non/api/route')
            ->assertStatus(404)
            ->assertJson(['message' => 'The route non/api/route could not be found.']);
    }
}
