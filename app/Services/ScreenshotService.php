<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\UserScreenshotDetailResource;
use App\Models\Screenshot;

class ScreenshotService extends BaseService
{
    protected UserService $userService;

    protected MeetingService $meetingService;

    public function __construct(Screenshot $screenshot, UserService $userService, MeetingService $meetingService)
    {
        $this->model = $screenshot;
        $this->userService = $userService;
        $this->meetingService = $meetingService;
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
}
