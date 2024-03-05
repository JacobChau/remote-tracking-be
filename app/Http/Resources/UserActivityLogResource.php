<?php

namespace App\Http\Resources;

use App\Enums\ActivityAction;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserActivityLogResource extends JsonApiResource
{
    /**
     * @var string[]
     */
    protected array $attributes = [
        'action',
        'userEmail',
        'meetingId',
        'meetingTitle',
        'createdAt',
    ];

    public function toAttributes(Request $request): array
    {
        return [
            'action' => ActivityAction::getKey($this->action),
            'userEmail' => $this->user->email,
            'meetingId' => $this->meeting->id,
            'meetingTitle' => $this->meeting->title,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
