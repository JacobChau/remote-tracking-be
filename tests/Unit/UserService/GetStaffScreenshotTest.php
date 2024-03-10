<?php

namespace Tests\Unit\UserService;

use App\Enums\UserRole;
use App\Http\Resources\UserScreenshotResource;
use App\Models\Screenshot;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStaffScreenshotTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(UserService::class);
    }

    //    public function getStaffScreenshot(): array
    //    {
    //        $query = $this->model->query();
    //        $query->role(UserRole::STAFF);
    //
    //        return $this->getList(UserScreenshotResource::class, request()->all(), $query, ['screenshots']);
    //    }

    public function testGetStaffScreenshotSuccess()
    {
        $user = User::factory()->create();
        $user->role = UserRole::STAFF;
        $user->save();

        Screenshot::factory()->create(['user_id' => $user->id]);

        $result = $this->service->getStaffScreenshot();
        $this->assertCount(1, $result['data']);
    }

    public function testGetStaffScreenshotFail()
    {
        $result = $this->service->getStaffScreenshot();
        $this->assertCount(0, $result['data']);
    }
}
