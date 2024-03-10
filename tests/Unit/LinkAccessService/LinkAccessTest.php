<?php

namespace Tests\Unit\LinkAccessService;

use App\Services\LinkAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\BaseService\BaseServiceTest;

class LinkAccessTest extends BaseServiceTest
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(LinkAccessService::class);
    }
}
