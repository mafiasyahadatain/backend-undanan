<?php

namespace App\Controllers\Api;

use App\Request\AuthRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Routing\Controller;
use Core\Http\Respond;
use Core\Support\Time;
use Exception;
use Firebase\JWT\JWT;
use Throwable;

class AuthController extends Controller
{
    public function login(AuthRequest $request, JsonResponse $json): JsonResponse
{
    $valid = $request->validated();

    if ($valid->fails()) {
        return $json->errorBadRequest($valid->messages());
    }

    // ✅ Bypass login tanpa Auth::attempt
    if (
        $valid['email'] !== 'admin@demo.com' ||
        $valid['password'] !== 'admin123'
    ) {
        return $json->error(Respond::HTTP_UNAUTHORIZED);
    }

    // ✅ Simulasikan user login
    auth()->login([
        'id' => 1,
        'name' => 'Demo Admin',
        'email' => 'admin@demo.com',
        'is_active' => true
    ]);

    if (!auth()->user()->isActive()) {
        return $json->errorBadRequest(['user not active.']);
    }

    if (!env('JWT_KEY')) {
        return $json->errorBadRequest(['JWT Key not found!.']);
    }

    $time = Time::factory()->getTimestamp();
    $token = JWT::encode(
        [
            'iat' => $time,
            'exp' => $time + (60 * 60),
            'iss' => base_url(),
            'sub' => strval(auth()->id()),
        ],
        env('JWT_KEY'),
        env('JWT_ALGO', 'HS256')
    );

    return $json->successOK([
        'token' => $token,
        'user' => auth()->user()->only(['name', 'email'])
    ]);
}
}
