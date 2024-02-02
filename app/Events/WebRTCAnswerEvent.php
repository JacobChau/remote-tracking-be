<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebRTCAnswerEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $meetingId;
    public string $answer;

    public function __construct($meetingId, $answer)
    {
        $this->meetingId = $meetingId;
        $this->answer = $answer;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.' . $this->meetingId);
    }
}

