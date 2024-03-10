<?php

namespace Tests\Unit\UserService;

use App\Models\Screenshot;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetListUserTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(UserService::class);
    }

    //    public function getList(?string $resourceClass = null, array $input = [], ?Builder $query = null, array $relations = []): array
    //    {
    //        $query = $this->model->query();
    //        if (isset($input['searchKeyword'])) {
    //            $query->where('name', 'like', '%'.$input['searchKeyword'].'%');
    //            $query->orWhere('email', 'like', '%'.$input['searchKeyword'].'%');
    //        }
    //
    //        return parent::getList($resourceClass, $input, $query, $relations);
    //    }

    public function testGetListUser()
    {
        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create(['user_id' => $user->id]);
        $input = ['searchKeyword' => $user->name];
        $query = User::query();
        $query->where('name', 'like', '%'.$input['searchKeyword'].'%');
        $query->orWhere('email', 'like', '%'.$input['searchKeyword'].'%');
        $relations = [];
        $result = $this->service->getList(null, $input, $query, $relations);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
