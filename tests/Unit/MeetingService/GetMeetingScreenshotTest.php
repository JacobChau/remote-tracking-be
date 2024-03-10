<?php

namespace MeetingService;

use App\Http\Resources\MeetingScreenshotResource;
use App\Models\Meeting;
use App\Models\Screenshot;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetMeetingScreenshotTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

    //    public function getMeetingScreenshot(): array
    //    {
    //        $query = $this->model->query();
    //
    //        return $this->getList(MeetingScreenshotResource::class, request()->all(), $query, ['screenshots']);
    //    }

    public function testGetMeetingScreenshot()
    {
        $meeting = Meeting::factory()->create();
        $screenshot = Screenshot::factory()->create(['meeting_id' => $meeting->id]);
        $response = $this->service->getMeetingScreenshot();
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
    }
}
