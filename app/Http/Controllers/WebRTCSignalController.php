<?php

namespace App\Http\Controllers;

use App\Events\WebRTCAnswerEvent;
use App\Events\WebRTCIceCandidateEvent;
use App\Events\WebRTCOfferEvent;
use App\Events\WebRTCSignalEvent;
use App\Models\Meeting;
use App\Services\MeetingService;
use App\Services\WebRTCSignalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebRTCSignalController extends Controller
{
    protected WebRTCSignalService $webRTCSignalingService;
    public function __construct(WebRTCSignalService $webRTCSignalingService)
    {
        $this->webRTCSignalingService = $webRTCSignalingService;
    }

    public function sendSignal(Request $request, $connectionId): JsonResponse
    {
        $this->webRTCSignalingService->sendSignal($connectionId, $request->signal);

        return response()->json(['status' => 'signal_sent']);
    }

    public function sendOffer(Request $request, string $meetingId): JsonResponse
    {
        $this->webRTCSignalingService->sendOffer($meetingId, $request->offer);

        return response()->json(['status' => 'offer_sent']);
    }

    public function sendAnswer(Request $request, string $meetingId): JsonResponse
    {
        $this->webRTCSignalingService->sendAnswer($meetingId, $request->answer);

        return response()->json(['status' => 'answer_sent']);
    }

    public function sendIceCandidate(Request $request, string $meetingId): JsonResponse
    {
        $this->webRTCSignalingService->sendIceCandidate($meetingId, $request->iceCandidate);

        return response()->json(['status' => 'ice_candidate_sent']);
    }
}
