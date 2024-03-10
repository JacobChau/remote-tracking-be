<?php

namespace AuthService;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;
    private AuthService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AuthService::class);
    }

    public function testRefreshToken()
    {
        $user = User::factory()->create();
        $refreshToken = Str::random(32);
        $user->remember_token = $refreshToken;
        $user->save();

        $response = $this->service->refreshToken($refreshToken);

        // Assert that response is an array with specific keys
        $this->assertIsArray($response);
        $this->assertArrayHasKey('accessToken', $response);
        $this->assertArrayHasKey('refreshToken', $response);

        // Assert that the values are strings (or not empty)
        $this->assertIsString($response['accessToken']);
        $this->assertIsString($response['refreshToken']);
        $this->assertNotEmpty($response['accessToken']);
        $this->assertNotEmpty($response['refreshToken']);

        $updatedUser = $user->fresh();
        $this->assertNotEquals($refreshToken, $updatedUser->remember_token);
        $this->assertEquals($response['refreshToken'], $updatedUser->remember_token);
    }

    public function testRefreshTokenNotFound()
    {
        $response = $this->service->refreshToken(Str::random(32));
        $this->assertEmpty($response);
    }

}
