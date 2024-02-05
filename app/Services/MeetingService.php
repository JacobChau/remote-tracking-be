<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LinkAccessType;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;

class MeetingService extends BaseService
{
    protected LinkService $linkService;
    protected LinkAccessService $linkAccessService;
    public function __construct(Meeting $meeting, LinkService $linkService, LinkAccessService $linkAccessService)
    {
        $this->model = $meeting;
        $this->linkService = $linkService;
        $this->linkAccessService = $linkAccessService;
    }

    public function join(string $hash): JsonResponse
    {

        $linkSetting = $this->linkService->findOneOrFail(['hash' => $hash]);

        if (!$linkSetting->is_enabled) {
            return response()->json(['status' => 'link_disabled'], 403);
        }

        if ($linkSetting->end_date && $linkSetting->end_date < now()) {
            return response()->json(['status' => 'link_expired'], 403);
        }

        if ($linkSetting->start_date > now()) {
            return response()->json(['status' => 'link_not_started'], 403);
        }

        if ($linkSetting->access_type === LinkAccessType::PRIVATE) {
            $staffId = auth()->user()->id;
            $linkAccess = $this->linkAccessService->findOneOrFail(['link_id' => $linkSetting->id, 'user_id' => $staffId]);
            if (!$linkAccess) {
                return response()->json(['status' => 'access_denied'], 403);
            }
        }

        $meeting = $linkSetting->meeting;

        return response()->json(new MeetingResource($meeting));
    }
}
