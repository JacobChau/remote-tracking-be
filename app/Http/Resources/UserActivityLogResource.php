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
        $action = $this->action;
        if ($action instanceof ActivityAction) {
            $action = $action->key;
        } else {
            $action = ActivityAction::getKey($action);
        }

        return [
            'action' => $action,
            'userEmail' => $this->user->email,
            'meetingId' => $this->meeting->id,
            'meetingTitle' => $this->meeting->title,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
