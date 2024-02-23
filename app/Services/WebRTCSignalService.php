<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
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

    public function sendSignal($meetingId, $signal, $staffId = null): void
    {
        $toAdmin = auth()->user()->role !== UserRole::ADMIN;
        event(new WebRTCSignalEvent($signal, $meetingId, $toAdmin, $staffId));
    }

    public function sendOffer(string $meetingId, string $staffId, array $offer): void
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);
        $meeting->offer = ['staffId' => $staffId, 'sdp' => $offer];
        $meeting->save();

        broadcast(new WebRTCOfferEvent($meetingId, $staffId, $offer))->toOthers();
    }

    public function sendAnswer(string $meetingId, string $staffId, array $answer): void
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);
        $meeting->answer = ['staffId' => $staffId, 'sdp' => $answer];
        $meeting->save();

        broadcast(new WebRTCAnswerEvent($meetingId, $staffId, $answer))->toOthers();
    }

    public function sendIceCandidate(string $meetingId, string $staffId, array $iceCandidate): void
    {
        $meeting = $this->meetingService->findOneOrFail(['id' => $meetingId]);
        $candidates = $meeting->ice_candidates ?? [];
        $candidates[] = ['staffId' => $staffId, 'candidate' => $iceCandidate];
        $meeting->ice_candidates = $candidates;
        $meeting->save();

        broadcast(new WebRTCIceCandidateEvent($meetingId, $staffId, $iceCandidate))->toOthers();
    }
}
