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

    const TURN_ON_MIC = 4;

    const TURN_OFF_MIC = 5;

    const START_SCREEN_SHARING = 6;

    const STOP_SCREEN_SHARING = 7;

    const NO_FACE_DETECTED = 8;
}
