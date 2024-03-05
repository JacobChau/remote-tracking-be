<?php

namespace App\Http\Controllers;

use App\Events\MeetingEvent;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use ReflectionException;

class MeetingController extends Controller
{
    protected MeetingService $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * @throws ReflectionException
     */
    public function index(): JsonResponse
    {
        $meetings = $this->meetingService->getList(MeetingResource::class, request()->all());

        return response()->json($meetings);
    }

    public function join(string $hash): JsonResponse
    {
        return $this->meetingService->join($hash);
    }

    public function leave(string $meetingId): JsonResponse
    {
        $staffId = auth()->user()->id ?? 1;

        broadcast(new MeetingEvent($meetingId, $staffId, 'left'))->toOthers();

        return response()->json(['status' => 'left']);
    }

    public function show(string $meetingId): JsonResponse
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);

        return response()->json(new MeetingResource($meeting));
    }

    public function store(StoreMeetingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->meetingService->create($data);

        return response()->json(['hash' => $response->hash]);
    }

    public function showByHash(string $hash): JsonResponse
    {
        // hash is store in the link setting, foreign key to the meeting
        $meeting = $this->meetingService->showByHash($hash);

        return response()->json(new MeetingResource($meeting));
    }
}
