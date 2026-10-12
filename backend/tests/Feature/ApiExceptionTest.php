<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiExceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_thrown_api_exception_uses_the_error_envelope(): void
    {
        Route::get('/api/testing/boom', function () {
            throw new ApiException(
                message: 'Custom failure',
                status: 409,
                errors: ['info' => 'taken'],
            );
        });

        $this->getJson('/api/testing/boom')
            ->assertStatus(409)
            ->assertExactJson([
                'status' => 'error',
                'message' => 'Custom failure',
                'errors' => ['info' => 'taken'],
            ]);
    }

    public function test_an_unknown_api_route_returns_route_not_found(): void
    {
        $this->getJson('/api/does-not-exist')
            ->assertNotFound()
            ->assertJsonPath('message', 'Route not found.');
    }

    public function test_a_missing_model_returns_resource_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tech-stack-groups/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_an_authorization_failure_returns_access_restricted(): void
    {
        Route::get('/api/testing/restricted', function () {
            throw new AuthorizationException;
        });

        $this->getJson('/api/testing/restricted')
            ->assertForbidden()
            ->assertJsonPath('message', 'You are not allowed to perform this action.');
    }
}
