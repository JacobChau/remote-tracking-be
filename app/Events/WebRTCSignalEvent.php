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

    public $signal;
    private $toAdmin;
    private $meetingId;
    public $staffId;

    public function __construct($signal, $meetingId, $toAdmin = false, $staffId = null)
    {
        $this->signal = $signal;
        $this->meetingId = $meetingId;
        $this->toAdmin = $toAdmin;
        $this->staffId = $staffId;
    }

    public function broadcastOn(): Channel
    {
        if ($this->toAdmin) {
            return new Channel('webrtc.admin.' . $this->meetingId);
        }

        return new Channel('webrtc.staff.' . $this->meetingId . '.' . $this->staffId);
    }
}
