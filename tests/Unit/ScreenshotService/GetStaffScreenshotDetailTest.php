<?php

namespace ScreenshotService;

use App\Models\Screenshot;
use App\Models\User;
use App\Services\ScreenshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStaffScreenshotDetailTest extends TestCase
{
    use RefreshDatabase;

    private ScreenshotService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ScreenshotService::class);
    }

    public function testGetStaffScreenshotDetail()
    {
        // Arrange
        $user = User::factory()->create();
        $screenshots = Screenshot::factory()->count(3)->create(['user_id' => $user->id]);

        // Act
        $result = $this->service->getStaffScreenshotDetail($user->id);

        // Assert
        $this->assertCount(3, $result['data']);
        $this->assertEquals(3, $result['meta']['total']);
    }

    public function testGetStaffScreenshotDetailWithFilters()
    {
        // Arrange
        $user = User::factory()->create();
        $screenshots = Screenshot::factory()->count(3)->create(['user_id' => $user->id, 'created_at' => now()->subDays()]);
        $filters = ['createdAt' => now()->format('Y-m-d')];

        $this->app['request']->merge(['filters' => $filters]);

        // Act
        $result = $this->service->getStaffScreenshotDetail($user->id);

        // Assert
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['meta']['total']);
    }
}
