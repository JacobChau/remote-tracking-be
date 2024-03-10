<?php

namespace ActivityLogService;

use App\Http\Resources\UserActivityLogResource;
use App\Models\ActivityLog;
use App\Models\Meeting;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GetStaffMeetingActivityLogTest extends TestCase
{
    use RefreshDatabase;
    private ActivityLogService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityLogService::class);
    }

    public function testGetStaffMeetingActivityLog(): void
    {
        // Arrange
        $user = User::factory()->create();
        $meeting = Meeting::factory()->create();
        $staffId = $user->id;
        $meetingId = $meeting->id;
        $activityLog = ActivityLog::factory()->create([
            'user_id' => $staffId,
            'meeting_id' => $meetingId,
        ]);

        // Act
        $result = $this->service->getStaffMeetingActivityLog($staffId, $meetingId);

        // Assert
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(UserActivityLogResource::class, $result['data']);
    }
}
