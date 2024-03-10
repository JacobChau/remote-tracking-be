<?php

namespace App\Events;

use App\Http\Resources\UserActivityLogResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserActivityLogResource $log;
    public int $staffId;
    public int $meetingId;
    public string $staffName;

    /**
     * Create a new event instance.
     */
    public function __construct(UserActivityLogResource $log, int $staffId, int $meetingId, string $staffName)
    {
        $this->log = $log;
        $this->staffId = $staffId;
        $this->meetingId = $meetingId;
        $this->staffName = $staffName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('monitor-meeting-'.$this->meetingId);
    }
}
