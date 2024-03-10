<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserScreenshotDetailResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'createdAt',
        'screenshotUrl',
        'avatarUrl',
        'meetingTitle',
        'userName',
    ];

    public function toAttributes(Request $request): array
    {
        return [
            'screenshotUrl' => $this->image_key ?? null,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'meetingTitle' => $this->when($this->meeting, function () {
                return $this->meeting->title;
            }),
            'userName' => $this->when($this->user, function () {
                return $this->user->name;
            }),
            'avatarUrl' => $this->when($this->user, function () {
                return $this->user->avatar;
            }),
        ];
    }
}
