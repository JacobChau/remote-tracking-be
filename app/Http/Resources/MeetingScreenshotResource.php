<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class MeetingScreenshotResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'title',
        'screenshotUrl',
    ];

    public function toAttributes(Request $request): array
    {
        $screenshot = $this->screenshots->first();

        return [
            'title' => $this->title,
            'screenshotUrl' => $screenshot->image_key ?? null,
        ];
    }
}
