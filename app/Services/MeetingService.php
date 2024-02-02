<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Meeting;

class MeetingService extends BaseService
{
    public function __construct(Meeting $meeting)
    {
        $this->model = $meeting;
    }
}
