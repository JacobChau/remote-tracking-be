<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LinkAccess;

class LinkAccessService extends BaseService
{
    public function __construct(LinkAccess $meeting)
    {
        $this->model = $meeting;
    }
}
