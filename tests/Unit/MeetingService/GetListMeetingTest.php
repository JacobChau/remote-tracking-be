<?php

namespace MeetingService;

use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetListMeetingTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

    public function testGetListMeeting()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $meeting = Meeting::factory()->create();

        $response = $this->service->getList(null, ['filters' => ['startDate' => '2021-01-01', 'endDate' => '2021-12-31']]);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testGetListMeetingWithFilters()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $meeting = Meeting::factory()->create();

        $this->app['request']->merge(['filters' => ['startDate' => '2021-01-01', 'endDate' => '2021-12-31']]);

        $response = $this->service->getList(null, ['filters' => ['startDate' => '2021-01-01', 'endDate' => '2021-12-31']]);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }

    public function testGetListMeetingWithSort()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $meeting = Meeting::factory()->create();

        $this->app['request']->merge(['sort' => 'start_date:desc']);

        $response = $this->service->getList(null, ['sort' => 'start_date:desc']);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
    }
}
