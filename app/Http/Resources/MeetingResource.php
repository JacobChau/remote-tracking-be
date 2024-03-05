<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class MeetingResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        $link = $this->linkSetting->first();
        $isAdmin = auth()->user()->role === UserRole::ADMIN;

        return [
            'title' => $this->title,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'hash' => $link ? $link->hash : null,
            'createdAt' => $this->when($isAdmin, $this->created_at),
        ];
    }
}
