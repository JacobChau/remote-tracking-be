<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\UserActivityLogResource;
use App\Http\Resources\UserScreenshotDetailResource;
use App\Http\Resources\UserScreenshotResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService extends BaseService
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }


    public function findByRememberToken(string $refreshToken): ?User
    {
        return $this->model->query()->rememberToken($refreshToken)->first();
    }

    public function findByRole(int $role): Collection
    {
        return $this->model->query()->role($role)->get();
    }

    public function getStaffScreenshot(): array
    {
        $query = $this->model->query();
        $query->role(UserRole::STAFF);
        return $this->getList(UserScreenshotResource::class, request()->all(), $query, ['screenshots']);
    }
}
