<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ActivityAction;
use App\Http\Resources\UserScreenshotDetailResource;
use App\Models\Screenshot;
use Illuminate\Support\Facades\DB;

class ScreenshotService extends BaseService
{
    protected UserService $userService;

    protected MeetingService $meetingService;
    protected ActivityLogService $activityLogService;

    public function __construct(Screenshot $screenshot, UserService $userService, MeetingService $meetingService, ActivityLogService $activityLogService)
    {
        $this->model = $screenshot;
        $this->userService = $userService;
        $this->meetingService = $meetingService;
        $this->activityLogService = $activityLogService;
    }

    public function getStaffScreenshot(): array
    {
        return $this->userService->getStaffScreenshot();
    }

    public function getStaffScreenshotDetail(string $id): array
    {
        $query = $this->model->query();
        $query->where('user_id', $id);
        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['createdAt'])) {
                $query->whereDate('created_at', '=', $filters['createdAt']);
            }
        }

        return $this->getList(UserScreenshotDetailResource::class, request()->all(), $query, ['user']);
    }

    public function getMeetingScreenshot(): array
    {
        return $this->meetingService->getMeetingScreenshot();
    }

    public function getMeetingScreenshotDetail(string $id): array
    {
        $query = $this->model->query();
        $query->where('meeting_id', $id);
        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['createdAt'])) {
                $query->whereDate('created_at', '=', $filters['createdAt']);
            }
        }

        return $this->getList(UserScreenshotDetailResource::class, request()->all(), $query, ['meeting']);
    }

    public function detectFace(array $data): void
    {
        DB::beginTransaction();
        try {
            $screenShot = $this->create([
                'user_id' => $data['userId'],
                'meeting_id' => $data['meetingId'],
                'image_key' => $data['imageKey'],
            ]);

            // create log with foreign key of screenshot
            $this->activityLogService->create([
                'user_id' => $data['userId'],
                'meeting_id' => $data['meetingId'],
                'screenshot_id' => $screenShot->id,
                'action' => ActivityAction::NO_FACE_DETECTED,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
