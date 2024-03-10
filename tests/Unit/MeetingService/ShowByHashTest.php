<?php

namespace MeetingService;

use App\Enums\LinkAccessType;
use App\Models\LinkSetting;
use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ShowByHashTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

    public function testShowByHash(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $meeting = Meeting::factory()->create();
        $linkSetting = LinkSetting::factory()->create([
            'meeting_id' => $meeting->id,
            'access_type' => LinkAccessType::PUBLIC,
            'is_enabled' => true,
            'start_date' => now(),
            'end_date' => now()->addHour(),
            'hash' => '1234567890ab',
        ]);

        $response = $this->service->showByHash('1234567890ab');

        $this->assertEquals($meeting->id, $response->id);
        $this->assertEquals($meeting->title, $response->title);
    }
}
