<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginGoogleRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected UserService $userService;

    protected AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Refresh a token.
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $response = $this->authService->refreshToken($request->validated()['refreshToken']);

        if (! $response) {
            return $this->sendResponse(
                null,
                'Invalid token',
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->sendResponse([
            'accessToken' => $response['accessToken'],
            'refreshToken' => $response['refreshToken'],
        ], 'Token refreshed successfully');
    }

    /**
     * Login user with Google
     */
    public function loginWithGoogle(LoginGoogleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $response = $this->authService->loginWithGoogle($validated['accessToken']);

        if (! $response) {
            return $this->sendResponse(
                null,
                'Invalid credentials',
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->sendResponse([
            'accessToken' => $response['accessToken'],
            'refreshToken' => $response['refreshToken'],
        ], 'User logged in successfully');
    }
}
