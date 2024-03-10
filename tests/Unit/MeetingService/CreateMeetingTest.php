<?php

namespace MeetingService;

use App\Enums\LinkAccessType;
use App\Models\LinkSetting;
use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateMeetingTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

//    public function create(array $data): object
//    {
//        DB::beginTransaction();
//        try {
//            $meeting = parent::create([
//                'title' => $data['title'],
//                'start_date' => $data['startDate'] ?? null,
//                'end_date' => $data['endDate'] ?? null,
//            ]);
//
//            $linkSetting = $meeting->linkSetting()->create([
//                'access_type' => $data['accessType']->value,
//                'is_enabled' => true,
//                'start_date' => $data['startDate'] ?? null,
//                'end_date' => $data['endDate'] ?? null,
//                'hash' => substr(md5($meeting->id . microtime()), 0, 12),
//            ]);
//
//            if (isset($data['participants']) && is_array($data['participants'])) {
//                foreach ($data['participants'] as $userId) {
//                    $linkSetting->accesses()->create([
//                        'user_id' => $userId,
//                        'is_allowed' => true,
//                    ]);
//                }
//            }
//
//            DB::commit();
//        } catch (Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//
//        return $linkSetting;
//    }

    public function testCreateMeeting(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $data = [
            'title' => 'Test Meeting',
            'startDate' => '2022-01-01 00:00:00',
            'endDate' => '2022-01-01 01:00:00',
            'accessType' => LinkAccessType::PRIVATE(),
            'participants' => [User::factory()->create()->id],
        ];

        $meeting = $this->service->create($data);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
            'start_date' => '2022-01-01 00:00:00',
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertDatabaseHas('link_settings', [
            'access_type' => LinkAccessType::PRIVATE()->value,
            'is_enabled' => true,
            'start_date' => '2022-01-01 00:00:00',
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertDatabaseHas('link_accesses', [
            'user_id' => $data['participants'][0],
            'is_allowed' => true,
        ]);

        $this->assertInstanceOf(LinkSetting::class, $meeting);
    }

    public function testCreateMeetingWithoutParticipants(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $data = [
            'title' => 'Test Meeting',
            'startDate' => '2022-01-01 00:00:00',
            'endDate' => '2022-01-01 01:00:00',
            'accessType' => LinkAccessType::PRIVATE(),
        ];

        $meeting = $this->service->create($data);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
            'start_date' => '2022-01-01 00:00:00',
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertDatabaseHas('link_settings', [
            'access_type' => LinkAccessType::PRIVATE()->value,
            'is_enabled' => true,
            'start_date' => '2022-01-01 00:00:00',
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertInstanceOf(LinkSetting::class, $meeting);
    }

    public function testCreateMeetingWithStartDateOnly(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $data = [
            'title' => 'Test Meeting',
            'startDate' => '2022-01-01 00:00:00',
            'accessType' => LinkAccessType::PRIVATE(),
            'participants' => [User::factory()->create()->id],
        ];

        $meeting = $this->service->create($data);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
            'start_date' => '2022-01-01 00:00:00',
        ]);

        $this->assertDatabaseHas('link_settings', [
            'access_type' => LinkAccessType::PRIVATE()->value,
            'is_enabled' => true,
            'start_date' => '2022-01-01 00:00:00',
        ]);

        $this->assertDatabaseHas('link_accesses', [
            'user_id' => $data['participants'][0],
            'is_allowed' => true,
        ]);

        $this->assertInstanceOf(LinkSetting::class, $meeting);
    }

    public function testCreateMeetingWithEndDateOnly(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $data = [
            'title' => 'Test Meeting',
            'endDate' => '2022-01-01 01:00:00',
            'accessType' => LinkAccessType::PRIVATE(),
            'participants' => [User::factory()->create()->id],
        ];

        $meeting = $this->service->create($data);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertDatabaseHas('link_settings', [
            'access_type' => LinkAccessType::PRIVATE()->value,
            'is_enabled' => true,
            'end_date' => '2022-01-01 01:00:00',
        ]);

        $this->assertDatabaseHas('link_accesses', [
            'user_id' => $data['participants'][0],
            'is_allowed' => true,
        ]);

        $this->assertInstanceOf(LinkSetting::class, $meeting);
    }

    public function testCreateMeetingWithoutStartDateAndEndDate(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $data = [
            'title' => 'Test Meeting',
            'accessType' => LinkAccessType::PRIVATE(),
            'participants' => [User::factory()->create()->id],
        ];

        $meeting = $this->service->create($data);

        $this->assertDatabaseHas('meetings', [
            'title' => 'Test Meeting',
        ]);

        $this->assertDatabaseHas('link_settings', [
            'access_type' => LinkAccessType::PRIVATE()->value,
            'is_enabled' => true,
        ]);

        $this->assertDatabaseHas('link_accesses', [
            'user_id' => $data['participants'][0],
            'is_allowed' => true,
        ]);

        $this->assertInstanceOf(LinkSetting::class, $meeting);
    }

    public function testCreateMeetingFailed(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollback')->once();

        $this->expectException(\Exception::class);

        $this->mock(Meeting::class, function ($mock) {
            $mock->shouldReceive('create')->andThrow(new \Exception());
        });

        $dataFailed = [
            'title' => '',
            'startDate' => '2022-01-01 00:00:00',
            'endDate' => '2022-01-01 01:00:00',
            'createdBy' => 999,
        ];

        $this->service->create($dataFailed);
    }
}
