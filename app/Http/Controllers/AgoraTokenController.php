<?php

namespace App\Http\Controllers;

use App\Lib\Agora\RtcTokenBuilder;
use Illuminate\Http\Request;

class AgoraTokenController extends Controller
{
    public function generateToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->input('channelName');
        $uid = 0; // You can also pass a specific UID here
        $role = RtcTokenBuilder::RolePublisher;
        $expireTimeInSeconds = 3600; // Token validity duration
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpireTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs);

        return response()->json(['token' => $token]);
    }
}
