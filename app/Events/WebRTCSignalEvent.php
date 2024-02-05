<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignalEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $connectionId;
    public $signal;

    public function __construct($connectionId, $signal)
    {
        $this->connectionId = $connectionId;
        $this->signal = $signal;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('webrtc.' . $this->connectionId);
    }
}
