<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteMeetingRequest;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use App\Services\MeetingService;
use Exception;
use Illuminate\Http\JsonResponse;
use ReflectionException;

class MeetingController extends Controller
{
    protected MeetingService $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }


    public function index(): JsonResponse
    {
        $meetings = $this->meetingService->getList(MeetingResource::class, request()->all());

        return response()->json($meetings);
    }

    public function join(string $hash): JsonResponse
    {
        return $this->meetingService->join($hash);
    }

    public function show(string $meetingId): JsonResponse
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);

        return response()->json(new MeetingResource($meeting));
    }

    public function store(StoreMeetingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $linkSetting = $this->meetingService->create($data);

        return response()->json(['hash' => $linkSetting->hash]);
    }

    public function showByHash(string $hash): JsonResponse
    {
        $meeting = $this->meetingService->showByHash($hash);

        return response()->json(new MeetingResource($meeting));
    }

    /**
     * @throws Exception
     */
    public function update(Meeting $meeting, UpdateMeetingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->meetingService->updateMeeting($meeting, $data);

        return $this->sendResponse('Meeting updated successfully');
    }

    public function invite(Meeting $meeting, InviteMeetingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->meetingService->invite($meeting, $data['emails']);

        return $this->sendResponse('Invitation sent successfully');
    }
}
