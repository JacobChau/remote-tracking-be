<?php

namespace AuthService;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginGoogleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private AuthService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AuthService::class);
    }

    public function testLoginWithGoogle()
    {
        $accessToken = 'test';
        $fakeUserResponse = (object) [
            'email' => 'test@example.com',
            'picture' => 'http://example.com/avatar.jpg',
            'family_name' => 'Doe',
            'given_name' => 'John',
        ];

        Http::fake([
            'https://www.googleapis.com/oauth2/v3/userinfo*'.$accessToken => Http::response(json_encode($fakeUserResponse), 200),
        ]);

        $user = User::factory()->create([
            'email' => $fakeUserResponse->email,
            'avatar' => 'http://example.com/avatar.jpg',
            'name' => 'Doe John',
        ]);

        $this->service->loginWithGoogle($accessToken);

        $this->assertDatabaseHas('users', [
            'email' => $fakeUserResponse->email,
            'avatar' => $fakeUserResponse->picture,
            'name' => $fakeUserResponse->family_name.' '.$fakeUserResponse->given_name,
        ]);
    }
}
