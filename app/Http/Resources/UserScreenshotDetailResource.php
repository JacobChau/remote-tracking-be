<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserScreenshotDetailResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'name',
        'createdAt',
        'screenshotUrl',
    ];


    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'screenshotUrl' => $this->image_key,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
