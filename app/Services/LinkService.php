<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LinkSetting;

class LinkService extends BaseService
{
    public function __construct(LinkSetting $meeting)
    {
        $this->model = $meeting;
    }
}
