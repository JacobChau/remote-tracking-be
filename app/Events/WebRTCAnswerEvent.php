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
    public string $staffId;
    public array $answer;

    public function __construct($meetingId, $staffId, $answer)
    {
        $this->meetingId = $meetingId;
        $this->staffId = $staffId;
        $this->answer = $answer;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.' . $this->meetingId);
    }
}

