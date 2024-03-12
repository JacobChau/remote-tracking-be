<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Resources\UserScreenshotResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

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

    public function getStaffScreenshot(): array
    {
        $query = $this->model->query();
        $query->role(UserRole::STAFF);

        return $this->getList(UserScreenshotResource::class, request()->all(), $query, ['screenshots']);
    }

    public function getList(?string $resourceClass = null, array $input = [], ?Builder $query = null, array $relations = []): array
    {
        $query = $this->model->query();
        if (isset($input['searchKeyword'])) {
            $query->where('name', 'like', '%'.$input['searchKeyword'].'%');
            $query->orWhere('email', 'like', '%'.$input['searchKeyword'].'%');
        }

//        http://localhost:8000/api/users?searchKeyword=Ch%C3%A2u+Jacob&filters[notInMeeting]=30

        if (isset($input['filters'])) {
            $filters = $input['filters'];
            if (isset($filters['notInMeeting'])) {
                $query->whereDoesntHave('meetings', function ($query) use ($filters) {
                    $query->where('meeting_id', $filters['notInMeeting']);
                });
            }
        }

        return parent::getList($resourceClass, $input, $query, $relations);
    }
}
