<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScreenshotRequest;
use App\Services\ScreenshotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScreenshotController extends Controller
{
    protected ScreenShotService $screenShotService;

    public function __construct(ScreenShotService $screenShotService)
    {
        $this->screenShotService = $screenShotService;
    }

    /**
     * Display a listing of the resource.
     */
    public function getStaffScreenshot(): JsonResponse
    {
        return $this->sendResponse($this->screenShotService->getStaffScreenshot(), 'Staff screenshot retrieved successfully');
    }

    public function getStaffScreenshotDetail(string $id): JsonResponse
    {
        return $this->sendResponse($this->screenShotService->getStaffScreenshotDetail($id), 'Staff screenshot detail retrieved successfully');
    }

    public function getMeetingScreenshot(): JsonResponse
    {
        return $this->sendResponse($this->screenShotService->getMeetingScreenshot(), 'Meeting screenshot retrieved successfully');
    }

    public function getMeetingScreenshotDetail(string $id): JsonResponse
    {
        return $this->sendResponse($this->screenShotService->getMeetingScreenshotDetail($id), 'Meeting screenshot detail retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScreenshotRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->screenShotService->create([
            'user_id' => $data['userId'],
            'meeting_id' => $data['meetingId'],
            'image_key' => $data['imageKey'],
        ]);

        return $this->sendResponse($response, 'Screenshot created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
