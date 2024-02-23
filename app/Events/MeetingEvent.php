<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;

    public $staffId;

    public $action;

    public function __construct($meetingId, $staffId, $action)
    {
        $this->meetingId = $meetingId;
        $this->staffId = $staffId;
        $this->action = $action;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('meeting.'.$this->meetingId);
    }
}
