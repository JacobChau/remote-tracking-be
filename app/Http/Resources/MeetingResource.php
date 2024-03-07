<?php

namespace App\Http\Resources;

use App\Enums\LinkAccessType;
use App\Enums\UserRole;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class MeetingResource extends JsonApiResource
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidEnumMemberException
     */
    public function toAttributes(Request $request): array
    {
        $link = $this->linkSetting->first();
        $isAdmin = auth()->user()->role === UserRole::ADMIN;
        $participants = $this->when($isAdmin, $link ? $link->accesses->map(fn ($access) => new UserResource($access->user)) : null);

        return [
            'title' => $this->title,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'hash' => $link ? $link->hash : null,
            'participants' => $participants,
            'linkEnabled' => $this->when($isAdmin, $link ? $link->is_enabled : null),
            'accessType' => $this->when($isAdmin, $link ? LinkAccessType::getKey($link->access_type) : null),
            'createdAt' => $this->when($isAdmin, $this->created_at),
        ];
    }
}
