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
        $data = [
            'title' => $this->title,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'hash' => $this->hash,
        ];

        if ($request->user()->role === UserRole::ADMIN) {
            $data['offer'] = $this->offer;
            $data['answer'] = $this->answer;
            $data['iceCandidates'] = $this->ice_candidates;
        }

        return $data;
    }
}
