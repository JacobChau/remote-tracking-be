<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ActivityAction;
use App\Events\ActivityLogCreatedEvent;
use App\Http\Resources\UserActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ReflectionException;

class ActivityLogService extends BaseService
{
    protected Request $request;

    public function __construct(ActivityLog $activityLog, Request $request)
    {
        $this->model = $activityLog;
        $this->request = $request;
    }

    private function applyFiltersAndSorts(Builder $query, array $conditions = []): Builder
    {
        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        // Apply filters from request
        if ($this->request->has('filters')) {
            $filters = $this->request->get('filters');
            $query->when($filters['startedAt'] ?? null, fn ($query) => $query->whereDate('created_at', '>=', $filters['startedAt']))
                ->when($filters['endedAt'] ?? null, fn ($query) => $query->whereDate('created_at', '<=', $filters['endedAt']))
                ->when($filters['action'] ?? null, fn ($query) => $query->where('action', ActivityAction::getValue($filters['action'])));
        }

        // Apply sorting from request
        $this->request->whenHas('sort', function ($sort) use ($query) {
            $sortParts = explode(':', $sort);
            $query->orderBy($sortParts[0], $sortParts[1] ?? 'asc');
        });

        return $query;
    }

    /**
     * @throws ReflectionException
     */
    public function getStaffActivityLog(string $userId): array
    {
        $query = $this->model->newQuery();
        $query = $this->applyFiltersAndSorts($query, ['user_id' => $userId]);

        return $this->getList(UserActivityLogResource::class, $this->request->all(), $query, ['user', 'meeting']);
    }

    /**
     * @throws ReflectionException
     */
    public function getStaffMeetingActivityLog(string $staffId, string $meetingId): array
    {
        $query = $this->model->newQuery();
        $query = $this->applyFiltersAndSorts($query, ['user_id' => $staffId, 'meeting_id' => $meetingId]);

        return $this->getList(UserActivityLogResource::class, $this->request->all(), $query, ['user', 'meeting']);
    }

    /**
     * @throws ReflectionException
     */
    public function getMeetingActivityLog(string $meetingId): array
    {
        $query = $this->model->newQuery();
        $query = $this->applyFiltersAndSorts($query, ['meeting_id' => $meetingId]);

        return $this->getList(UserActivityLogResource::class, $this->request->all(), $query, ['user', 'meeting']);
    }

    public function create(array $data): object
    {
        $log = $this->model->create($data);

        event(new ActivityLogCreatedEvent(new UserActivityLogResource($log), (int) $log->user_id, (int) $log->meeting_id, $log->user->name));

        return $log;
    }
}
