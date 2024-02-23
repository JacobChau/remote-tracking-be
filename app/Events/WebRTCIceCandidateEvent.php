<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCIceCandidateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $meetingId;

    public string $staffId;

    public array $candidate;

    public function __construct($meetingId, $staffId, $candidate)
    {
        $this->meetingId = $meetingId;
        $this->staffId = $staffId;
        $this->candidate = $candidate;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.'.$this->meetingId);
    }
}
