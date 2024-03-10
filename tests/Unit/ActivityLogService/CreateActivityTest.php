<?php

namespace ActivityLogService;

use App\Enums\ActivityAction;
use App\Models\Meeting;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CreateActivityTest extends TestCase
{
    use RefreshDatabase;
    private ActivityLogService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityLogService::class);
    }

    #[DataProvider('createActivityLogSuccessProvider')]
    public function testCreateActivityLog(array $data): void
    {
        // prepare data
        $user = User::factory()->create();
        $meeting = Meeting::factory()->create();
        $data['user_id'] = $user->id;
        $data['meeting_id'] = $meeting->id;

        $result = $this->service->create($data);
        $this->assertNotNull($result);
        $this->assertIsObject($result);
        $this->assertNotNull($result->id);
        $this->assertNotNull($result->user_id);
        $this->assertNotNull($result->meeting_id);
        $this->assertNotNull($result->action);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
    }

    public static function createActivityLogSuccessProvider(): array
    {
        return [
            'join meeting' => [
                [
                    'action' => ActivityAction::JOIN_MEETING,
                ]
            ],
            'leave meeting' => [
                [
                    'action' => ActivityAction::LEAVE_MEETING,
                ]
            ],
            'turn on camera' => [
                [
                    'action' => ActivityAction::TURN_ON_CAMERA,
                ]
            ],
            'turn off camera' => [
                [
                    'action' => ActivityAction::TURN_OFF_CAMERA,
                ]
            ],
            'turn on mic' => [
                [
                    'action' => ActivityAction::TURN_ON_MIC,
                ]
            ],
            'turn off mic' => [
                [
                    'action' => ActivityAction::TURN_OFF_MIC,
                ]
            ],
            'start screen sharing' => [
                [
                    'action' => ActivityAction::START_SCREEN_SHARING,
                ]
            ],
            'stop screen sharing' => [
                [
                    'action' => ActivityAction::STOP_SCREEN_SHARING,
                ]
            ],
            'no face detected' => [
                [
                    'action' => ActivityAction::NO_FACE_DETECTED,
                ]
            ]
        ];
    }

}
