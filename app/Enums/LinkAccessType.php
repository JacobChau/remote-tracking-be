<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LinkAccessType extends Enum
{
    const PUBLIC = 0;

    const PRIVATE = 1;
}
