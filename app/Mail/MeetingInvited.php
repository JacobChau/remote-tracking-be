<?php

namespace App\Mail;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeetingInvited extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Meeting $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function build(): MeetingInvited
    {
        $hash = $this->meeting->linkSetting->first() ? $this->meeting->linkSetting->first()->hash : null;
        $url = config('app.url').'/meetings/'.$hash;
        return $this->subject('You have been invited to a meeting')
            ->view('mails.meetingInvited')
            ->with([
                'url' => $url,
                'meeting' => $this->meeting,
            ]);
    }
}
