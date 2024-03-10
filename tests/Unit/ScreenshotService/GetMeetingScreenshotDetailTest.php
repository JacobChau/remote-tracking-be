<?php

namespace ScreenshotService;

use App\Models\Meeting;
use App\Models\Screenshot;
use App\Services\ScreenshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetMeetingScreenshotDetailTest extends TestCase
{
    use RefreshDatabase;

    private ScreenshotService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ScreenshotService::class);
    }

    public function testGetMeetingScreenshotDetail()
    {
        // Arrange
        $meeting = Meeting::factory()->create();
        $screenshots = Screenshot::factory()->count(3)->create(['meeting_id' => $meeting->id]);

        // Act
        $result = $this->service->getMeetingScreenshotDetail($meeting->id);

        // Assert
        $this->assertCount(3, $result['data']);
        $this->assertEquals(3, $result['meta']['total']);
    }

    public function testGetMeetingScreenshotDetailWithFilters()
    {
        // Arrange
        $meeting = Meeting::factory()->create();
        $screenshots = Screenshot::factory()->count(3)->create(['meeting_id' => $meeting->id, 'created_at' => now()->subDays()]);
        $filters = ['createdAt' => now()->format('Y-m-d')];

        $this->app['request']->merge(['filters' => $filters]);

        // Act
        $result = $this->service->getMeetingScreenshotDetail($meeting->id);

        // Assert
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['meta']['total']);
    }
}
