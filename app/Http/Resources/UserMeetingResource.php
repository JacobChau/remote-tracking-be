<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserMeetingResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        $link = $this->linkSetting->first();

        $userMeeting = $this->users->find(auth()->user()->id);

        return [
            'title' => $this->title,
            'hash' => $link ? $link->hash : null,
            'autoScreenshot' => $userMeeting->pivot->auto_screenshot,
        ];
    }
}
