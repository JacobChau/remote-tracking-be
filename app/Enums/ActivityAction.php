<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ActivityAction extends Enum
{
    const JOIN_MEETING = 0;
    const LEAVE_MEETING = 1;
    const TURN_ON_CAMERA = 2;
    const TURN_OFF_CAMERA = 3;
    const TURN_ON_SCREEN = 4;
    const TURN_OFF_SCREEN = 5;
}
