<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserActivityLogResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'action',
        'createdAt',
    ];

    public function toAttributes(Request $request): array
    {
        return [
            'action' => $this->action,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
