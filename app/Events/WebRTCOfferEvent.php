<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebRTCOfferEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $staffId;
    public array $offer;

    public function __construct($meetingId, $staffId, $offer)
    {
        $this->meetingId = $meetingId;
        $this->staffId = $staffId;
        $this->offer = $offer;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.' . $this->meetingId);
    }
}

