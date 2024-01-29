<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

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
}
