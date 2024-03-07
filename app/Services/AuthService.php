<?php

namespace App\Services;

use App\Enums\UserRole;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function loginWithGoogle(string $accessToken): array
    {
        $results = $this->getUserInfoFromGoogle($accessToken);

        $user = $this->userService->firstOrCreate([
            'email' => $results->email,
        ], [
            'avatar' => $results->picture,
            'name' => $results->family_name.' '.$results->given_name,
        ]
        );

        return $this->loginAndReturnToken($user);
    }

    public function refreshToken(string $refreshToken): array
    {
        $user = $this->userService->findByRememberToken($refreshToken);

        if (! $user) {
            return [];
        }

        $rememberToken = Str::random(32);

        $this->userService->update($user->id, [
            'remember_token' => $rememberToken,
        ]);

        return [
            'role' => UserRole::getKey($user->role),
            'accessToken' => auth()->tokenById($user->id),
            'refreshToken' => $rememberToken,
        ];
    }

    private function getUserInfoFromGoogle(string $accessToken)
    {
        $client = new Client();
        $response = $client->get('https://www.googleapis.com/oauth2/v3/userinfo?access_token='.$accessToken);

        return json_decode($response->getBody()->getContents());
    }

    private function loginAndReturnToken($user): array
    {
        auth()->login($user);

        $rememberToken = Str::random(32);

        $this->userService->update(auth()->id(), [
            'remember_token' => $rememberToken,
        ]);

        return [
            'role' => UserRole::getKey($user->role),
            'accessToken' => auth()->tokenById(auth()->id()),
            'refreshToken' => $rememberToken,
        ];

    }
}
