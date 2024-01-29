<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserRole extends Enum
{
    const STAFF = 0;

    const ADMIN = 1;
}
