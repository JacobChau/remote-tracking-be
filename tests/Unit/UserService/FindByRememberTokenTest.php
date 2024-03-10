<?php

namespace Tests\Unit\UserService;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByRememberTokenTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(UserService::class);
    }

    public function testFindByRememberTokenSuccess()
    {
        $user = User::factory()->create();
        $user->remember_token = 'test_token';
        $user->save();

        $result = $this->service->findByRememberToken('test_token');
        $this->assertEquals($user->id, $result->id);

        $user->delete();
    }

    public function testFindByRememberTokenFail()
    {
        $result = $this->service->findByRememberToken('test_token');
        $this->assertNull($result);
    }
}
