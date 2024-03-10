<?php

namespace ActivityLogService;

use App\Http\Resources\UserActivityLogResource;
use App\Models\ActivityLog;
use App\Models\Meeting;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetMeetingActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private ActivityLogService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityLogService::class);
    }

    public function testGetMeetingActivityLog(): void
    {
        // Arrange
        $meeting = Meeting::factory()->create();
        $meetingId = $meeting->id;
        $activityLog = ActivityLog::factory()->create([
            'meeting_id' => $meetingId,
        ]);

        // Act
        $result = $this->service->getMeetingActivityLog($meetingId);

        // Assert
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(UserActivityLogResource::class, $result['data']);
        $this->assertEquals($activityLog->id, $result['data'][0]->id);
    }
}
