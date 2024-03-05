<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\UserActivityLogResource;
use App\Http\Resources\UserScreenshotDetailResource;
use App\Http\Resources\UserScreenshotResource;
use App\Models\ActivityLog;
use App\Models\Screenshot;

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
        $query->whereHas('user', function ($query) {
            $query->where('role', UserRole::STAFF);
        });
        return $this->getList(UserActivityLogResource::class, request()->all(), $query, ['activityLogs']);
    }
}
