<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserScreenshotResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'name',
        'avatarUrl',
        'screenshotUrl',
    ];

    public function toAttributes(Request $request): array
    {
        $screenshot = $this->screenshots->first();

        return [
            'name' => $this->name,
            'avatarUrl' => $this->avatar,
            'screenshotUrl' => $screenshot->image_key ?? null,
        ];
    }
}
