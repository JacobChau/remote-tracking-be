<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function store(StoreActivityLogRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $response = $this->activityLogService->create([
            'action' => $validated['action'],
            'user_id' => $validated['userId'],
            'meeting_id' => $validated['meetingId'],
        ]);

        return $this->sendResponse($response, 'Activity log created successfully.');
    }

    public function getStaffActivityLog(string $userId): JsonResponse
    {
        $response = $this->activityLogService->getStaffActivityLog($userId);

        return $this->sendResponse($response, 'Activity log retrieved successfully.');
    }

    public function getStaffMeetingActivityLog(string $staffId, string $meetingId): JsonResponse
    {
        $response = $this->activityLogService->getStaffMeetingActivityLog($staffId, $meetingId);

        return $this->sendResponse($response, 'Activity log retrieved successfully.');
    }
}
