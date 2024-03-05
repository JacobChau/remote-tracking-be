<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\UserScreenshotDetailResource;
use App\Http\Resources\UserScreenshotResource;
use App\Models\Screenshot;

class ScreenshotService extends BaseService
{
    protected UserService $userService;

    public function __construct(Screenshot $screenshot, UserService $userService)
    {
        $this->model = $screenshot;
        $this->userService = $userService;
    }

    public function getStaffScreenshot(): array
    {
        return $this->userService->getStaffScreenshot();
    }

    public function getStaffScreenshotDetail(string $id): array
    {
//        http://localhost:8000/api/screenshots/staffs/2?page=1&perPage=6&filters[createdAt]=2024-02-27
        $query = $this->model->query();
        $query->where('user_id', $id);
        if (request()->has('filters')) {
            $filters = request()->get('filters');
            if (isset($filters['createdAt'])) {
                $query->whereDate('created_at', '=', $filters['createdAt']);
            }
        }
        return $this->getList(UserScreenshotDetailResource::class, request()->all(), $query);
    }
}
