<?php

namespace Tests\Unit\MeetingService;

use App\Enums\LinkAccessType;
use App\Models\LinkSetting;
use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class JoinMeetingTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

    public function testJoinMeetingSuccess()
    {
        $hash = 'hash';
        $linkSetting = LinkSetting::factory()->create(['hash' => $hash, 'is_enabled' => true, 'access_type' => LinkAccessType::PUBLIC, 'start_date' => now()->subDay(), 'end_date' => now()->addDay()]);
        $meeting = $linkSetting->meeting;
        $user = User::factory()->create();
        $meeting->users()->syncWithoutDetaching($user->id);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->service->join($hash);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
    }

    public function testJoinMeetingLinkDisabled()
    {
        $hash = 'hash';
        $linkSetting = LinkSetting::factory()->create(['hash' => $hash, 'is_enabled' => false]);
        $meeting = $linkSetting->meeting;
        $user = User::factory()->create();
        $meeting->users()->syncWithoutDetaching($user->id);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->service->join($hash);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('link_disabled', $response->getData()->status);
    }

    public function testJoinMeetingLinkExpired()
    {
        $hash = 'hash';
        $linkSetting = LinkSetting::factory()->create(['hash' => $hash, 'end_date' => now()->subDay(), 'is_enabled' => true, 'access_type' => LinkAccessType::PUBLIC]);
        $meeting = $linkSetting->meeting;
        $user = User::factory()->create();
        $meeting->users()->syncWithoutDetaching($user->id);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->service->join($hash);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('link_expired', $response->getData()->status);
    }

    public function testJoinMeetingLinkNotStarted()
    {
        $hash = 'hash';
        $linkSetting = LinkSetting::factory()->create(['hash' => $hash, 'start_date' => now()->addDay(), 'is_enabled' => true, 'access_type' => LinkAccessType::PUBLIC, 'end_date' => now()->addDay()]);
        $meeting = $linkSetting->meeting;
        $user = User::factory()->create();
        $meeting->users()->syncWithoutDetaching($user->id);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->service->join($hash);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('link_not_started', $response->getData()->status);
    }

    public function testJoinMeetingPrivateAccessDenied()
    {
        $hash = 'hash';
        $linkSetting = LinkSetting::factory()->create(['hash' => $hash, 'access_type' => LinkAccessType::PRIVATE, 'is_enabled' => true, 'start_date' => now()->subDay(), 'end_date' => now()->addDay()]);
        $meeting = $linkSetting->meeting;
        $user = User::factory()->create();
        $meeting->users()->syncWithoutDetaching($user->id);

        Auth::shouldReceive('user')->andReturn($user);

        $response = $this->service->join($hash);
        $this->assertEquals(403, $response->status());
        $this->assertEquals('access_denied', $response->getData()->status);
    }
}
