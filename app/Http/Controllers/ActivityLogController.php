<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function store(StoreActivityLogRequest $request): JsonResponse
    {
        $response = $this->activityLogService->create($request->validated());

        return $this->sendResponse($response, 'Activity log created successfully.');
    }

    public function getStaffActivityLog(): JsonResponse
    {
        $response = $this->activityLogService->getStaffActivityLog(request()->user()->id);

        return $this->sendResponse($response, 'Activity log retrieved successfully.');
    }
}
