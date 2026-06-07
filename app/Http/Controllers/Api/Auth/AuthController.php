<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Issues a JWT token for valid credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::guard('api')->user();

        if (!$user->is_active) {
            Auth::guard('api')->logout();
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        activity()->causedBy($user)->log('User logged in');

        return $this->respondWithToken($token);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        activity()->causedBy(Auth::guard('api')->user())->log('User logged out');
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * POST /api/auth/refresh
     * Refreshes the JWT token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * GET /api/auth/me
     * Returns the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * POST /api/auth/forgot-password
     * Sends a password reset link.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 400);
    }

    /**
     * POST /api/auth/reset-password
     * Resets the user's password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully.'])
            : response()->json(['message' => 'Invalid or expired reset token.'], 400);
    }

    // ── Private ───────────────────────────────────────────────
    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
            'user'         => new UserResource(Auth::guard('api')->user()),
        ]);
    }
}
