<?php

namespace ActivityLogService;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStaffActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private ActivityLogService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityLogService::class);
    }

    public function testGetStaffActivityLog()
    {
        // Arrange
        $user = User::factory()->create();
        $activityLog = ActivityLog::factory()->create(['user_id' => $user->id]);

        // Act
        $result = $this->service->getStaffActivityLog($user->id);

        // Assert
        $this->assertIsArray($result);
    }
}
