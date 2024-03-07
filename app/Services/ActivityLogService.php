<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ActivityAction;
use App\Http\Resources\UserActivityLogResource;
use App\Models\ActivityLog;

class ActivityLogService extends BaseService
{
    public function __construct(ActivityLog $activityLog)
    {
        $this->model = $activityLog;

    }

    public function getStaffActivityLog(string $userId): array
    {
        $query = $this->model->query();
        $query->where('user_id', $userId);
        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['startedAt'])) {
                $query->whereDate('created_at', '>=', $filters['startedAt']);
            }
            if (isset($filters['endedAt'])) {
                $query->whereDate('created_at', '<=', $filters['endedAt']);
            }
            if (isset($filters['action'])) {
                $query->where('action', ActivityAction::getValue($filters['action']));
            }
        }

        if (request()->has('sort')) {
            $sort = explode(':', request()->get('sort'));
            $query->orderBy($sort[0], $sort[1]);
        }

        return $this->getList(UserActivityLogResource::class, request()->all(), $query, ['user', 'meeting']);
    }

    public function getStaffMeetingActivityLog(string $staffId, string $meetingId): array
    {
        $query = $this->model->query();
        $query->where('user_id', $staffId);
        $query->where('meeting_id', $meetingId);

        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['startedAt'])) {
                $query->whereDate('created_at', '>=', $filters['startedAt']);
            }
            if (isset($filters['endedAt'])) {
                $query->whereDate('created_at', '<=', $filters['endedAt']);
            }
            if (isset($filters['action'])) {
                $query->where('action', ActivityAction::getValue($filters['action']));
            }
        }

        if (request()->has('sort')) {
            $sort = explode(':', request()->get('sort'));
            $query->orderBy($sort[0], $sort[1]);
        }

        return $this->getList(UserActivityLogResource::class, request()->all(), $query, ['user', 'meeting']);
    }

    public function getMeetingActivityLog(string $meetingId): array
    {
        $query = $this->model->query();
        $query->where('meeting_id', $meetingId);

        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['startedAt'])) {
                $query->whereDate('created_at', '>=', $filters['startedAt']);
            }
            if (isset($filters['endedAt'])) {
                $query->whereDate('created_at', '<=', $filters['endedAt']);
            }
            if (isset($filters['action'])) {
                $query->where('action', ActivityAction::getValue($filters['action']));
            }
        }

        if (request()->has('sort')) {
            $sort = explode(':', request()->get('sort'));
            $query->orderBy($sort[0], $sort[1]);
        }

        return $this->getList(UserActivityLogResource::class, request()->all(), $query, ['user', 'meeting']);
    }
}
