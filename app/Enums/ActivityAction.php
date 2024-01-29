<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ActivityAction extends Enum
{
    const JoinMeeting = 0;
    const LeaveMeeting = 1;
    const TurnOnCamera = 2;
    const TurnOffCamera = 3;
    const TurnOnScreen = 4;
    const TurnOffScreen = 5;
}
