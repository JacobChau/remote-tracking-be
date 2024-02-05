<?php

namespace App\Http\Controllers;

use App\Enums\LinkAccessType;
use App\Events\MeetingEvent;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Services\LinkAccessService;
use App\Services\LinkService;
use App\Services\MeetingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
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
        $meeting = $this->meetingService->create([
            'title' => $data['title'],
            'start_date' => $data['startDate'],
            'end_date' => $data['endDate'],
        ]);

        $meeting->linkSetting()->create([
            'access_type' => $data['accessType'],
            'is_enabled' => true,
            'start_date' => $data['startDate'],
            'end_date' => $data['endDate'],
            'hash' => Str::random(32),
        ]);

        return response()->json(new MeetingResource($meeting));
    }
}
