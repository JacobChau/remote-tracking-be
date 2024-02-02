<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\WebRTCAnswerEvent;
use App\Events\WebRTCIceCandidateEvent;
use App\Events\WebRTCOfferEvent;
use App\Events\WebRTCSignalEvent;

class WebRTCSignalService extends BaseService
{
    protected MeetingService $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    public function sendSignal(string $connectionId, array $signal): void
    {
        event(new WebRTCSignalEvent($connectionId, $signal));
    }

    public function sendOffer(string $meetingId, array $offer): void
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);
        $meeting->offer = $offer;
        $meeting->save();

        broadcast(new WebRTCOfferEvent($meetingId, $offer))->toOthers();
    }

    public function sendAnswer(string $meetingId, array $answer): void
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);
        $meeting->answer = $answer;
        $meeting->save();

        broadcast(new WebRTCAnswerEvent($meetingId, $answer))->toOthers();
    }

    public function sendIceCandidate(string $meetingId, array $iceCandidate): void
    {
        broadcast(new WebRTCIceCandidateEvent($meetingId, $iceCandidate))->toOthers();
    }

}
