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
    public $offer;

    public function __construct($meetingId, $offer)
    {
        $this->meetingId = $meetingId;
        $this->offer = $offer;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.' . $this->meetingId);
    }
}

