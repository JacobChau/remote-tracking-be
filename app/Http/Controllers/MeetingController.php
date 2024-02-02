<?php

namespace App\Http\Controllers;

use App\Events\MeetingEvent;
use App\Http\Resources\MeetingResource;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;

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

    public function join(string $meetingId): JsonResponse
    {
        $staffId = auth()->user()->id ?? 1;

        var_dump($staffId);

        // Logic to join the meeting...

        // Broadcast that the staff has joined
        broadcast(new MeetingEvent($meetingId, $staffId, 'joined'))->toOthers();

        return response()->json(['status' => 'joined', 'meetingId' => $meetingId]);
    }

    public function leave(string $meetingId): JsonResponse
    {
        $staffId = auth()->user()->id ?? 1;

        broadcast(new MeetingEvent($meetingId, $staffId, 'left'))->toOthers();

        return response()->json(['status' => 'left']);
    }
}
