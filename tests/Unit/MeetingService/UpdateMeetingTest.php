<?php

namespace MeetingService;

use App\Enums\LinkAccessType;
use App\Models\LinkSetting;
use App\Models\Meeting;
use App\Models\User;
use App\Services\MeetingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UpdateMeetingTest extends TestCase
{
    use RefreshDatabase;

    private MeetingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MeetingService::class);
    }

//    public function updateMeeting(Meeting $meeting, array $data): void
//    {
//        DB::beginTransaction();
//        try {
//            $linkSetting = $meeting->linkSetting()->firstOrFail();
//
//            if (isset($data['participants']) && is_array($data['participants'])) {
//                $currentParticipantIds = $linkSetting->accesses()->pluck('user_id')->toArray();
//                $newParticipantIds = $data['participants'];
//
//                // Compute the participants to add and to remove
//                $participantIdsToAdd = array_diff($newParticipantIds, $currentParticipantIds);
//                $participantIdsToRemove = array_diff($currentParticipantIds, $newParticipantIds);
//
//                // Add new participants
//                foreach ($participantIdsToAdd as $userId) {
//                    $linkSetting->accesses()->create([
//                        'user_id' => $userId,
//                        'is_allowed' => true,
//                    ]);
//                }
//
//                // Remove participants not in the new list
//                if (!empty($participantIdsToRemove)) {
//                    $linkSetting->accesses()->whereIn('user_id', $participantIdsToRemove)->delete();
//                }
//            }
//
//            if (isset($data['linkEnabled'])) {
//                $linkSettingData['is_enabled'] = $data['linkEnabled'];
//            }
//
//            if (isset($data['accessType'])) {
//                $linkSettingData['access_type'] = $data['accessType']->value;
//            }
//
//            if (isset($data['startDate'])) {
//                $linkSettingData['start_date'] = $data['startDate'];
//            }
//
//            if (isset($data['endDate'])) {
//                $linkSettingData['end_date'] = $data['endDate'];
//            }
//
//            $linkSetting->update($linkSettingData);
//
//            // Prepare meeting updates
//            $meetingData = collect($data)->only(['title', 'startDate', 'endDate'])->filter()->all();
//            $meeting->update($meetingData);
//
//            DB::commit();
//        } catch (Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//    }

    public function testUpdateMeeting(): void
    {
        $user = User::factory()->create();
        $meeting = Meeting::factory()->create();
        $linkSetting = LinkSetting::factory()->create(['meeting_id' => $meeting->id]);
        $linkSetting->accesses()->create(['user_id' => $user->id, 'is_allowed' => true]);

        $data = [
            'participants' => [$user->id],
            'linkEnabled' => true,
            'accessType' => new LinkAccessType(LinkAccessType::PUBLIC),
            'startDate' => now(),
            'endDate' => now()->addDay(),
            'title' => 'New title',
        ];

        $this->service->updateMeeting($meeting, $data);

        $this->assertDatabaseHas('link_settings', [
            'id' => $linkSetting->id,
            'is_enabled' => true,
            'access_type' => LinkAccessType::PUBLIC,
            'start_date' => $data['startDate'],
            'end_date' => $data['endDate'],
        ]);

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'title' => $data['title'],
        ]);

        $this->assertDatabaseHas('link_accesses', [
            'link_id' => $linkSetting->id,
            'user_id' => $user->id,
            'is_allowed' => true,
        ]);
    }

    public function testUpdateMeetingWithParticipants(): void
    {
        $user = User::factory()->create();
        $meeting = Meeting::factory()->create();
        $linkSetting = LinkSetting::factory()->create(['meeting_id' => $meeting->id]);
        $linkSetting->accesses()->create(['user_id' => $user->id, 'is_allowed' => true]);

        $newUser = User::factory()->create();
        $data = [
            'participants' => [$newUser->id],
        ];

        $this->service->updateMeeting($meeting, $data);

        $this->assertDatabaseHas('link_accesses', [
            'link_id' => $linkSetting->id,
            'user_id' => $newUser->id,
            'is_allowed' => true,
        ]);

        $this->assertDatabaseMissing('link_accesses', [
            'link_id' => $linkSetting->id,
            'user_id' => $user->id,
        ]);
    }

    public function testUpdateMeetingWithLinkEnabled(): void
    {
        $meeting = Meeting::factory()->create();
        $linkSetting = LinkSetting::factory()->create(['meeting_id' => $meeting->id]);

        $data = [
            'linkEnabled' => true,
        ];

        $this->service->updateMeeting($meeting, $data);

        $this->assertDatabaseHas('link_settings', [
            'id' => $linkSetting->id,
            'is_enabled' => true,
        ]);
    }

    public function testUpdateMeetingFail(): void
    {
        $meeting = Meeting::factory()->create();
        $data = [
            'participants' => [1],
        ];

        $this->expectException(\Exception::class);
        $this->service->updateMeeting($meeting, $data);
    }
}
