<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LinkAccessType;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\MeetingScreenshotResource;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Builder;
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

        if (! $linkSetting->is_enabled) {
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
            if (! $linkAccess) {
                return response()->json(['status' => 'access_denied'], 403);
            }
        }

        $meeting = $linkSetting->meeting;

        return response()->json(new MeetingResource($meeting));
    }

    public function getMeetingScreenshot(): array
    {
        $query = $this->model->query();

        return $this->getList(MeetingScreenshotResource::class, request()->all(), $query, ['screenshots']);
    }

    public function create(array $data): object
    {
        $meeting = parent::create([
            'title' => $data['title'],
            'start_date' => $data['startDate'] ?? null,
            'end_date' => $data['endDate'] ?? null,
        ]);

        $linkSetting = $meeting->linkSetting()->create([
            'access_type' => $data['accessType']->value,
            'is_enabled' => true,
            'start_date' => $data['startDate'] ?? null,
            'end_date' => $data['endDate'] ?? null,
            'hash' => substr(md5($meeting->id.microtime()), 0, 12),
        ]);

        if (isset($data['participants']) && is_array($data['participants'])) {
            foreach ($data['participants'] as $userId) {
                $linkSetting->accesses()->create([
                    'user_id' => $userId,
                    'is_allowed' => true,
                ]);
            }
        }

        // return hash
        return $linkSetting;
    }

    public function showByHash(string $hash): object
    {
        // hash is store in the link setting, foreign key to the meeting
        $linkSetting = $this->linkService->findOneOrFail(['hash' => $hash]);

        return $linkSetting->meeting;
    }

    public function getList(?string $resourceClass = null, array $input = [], ?Builder $query = null, array $relations = []): array
    {
        $query = $this->model->query();

        //        http://localhost:8000/api/meetings?searchKeyword=&sort=created_at:desc&page=1&perPage=8&filters[startDate]=2024-02-09&filters[endDate]=2024-03-03

        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['startDate'])) {
                $query->whereDate('start_date', '>=', $filters['startDate']);
            }

            if (isset($filters['endDate'])) {
                $query->whereDate('end_date', '<=', $filters['endDate']);
            }
        }

        if (request()->has('sort')) {
            $sort = explode(':', request()->get('sort'));
            $query->orderBy($sort[0], $sort[1]);
        }

        return parent::getList($resourceClass, $input, $query, $relations);

    }
}
