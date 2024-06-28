<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_api_route_error_shown_when_requesting_an_invalid_route()
    {
        $this->getJsonApi('api/test')
            ->assertStatus(404)
            ->assertJsonApiError(
                title: 'Not Found',
                status: '404',
                detail: 'The route api/test could not be found.',
            );
    }
}
