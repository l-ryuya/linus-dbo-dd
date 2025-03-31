<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 認証認可はプロトタイプ以降では別システムになる想定であり
 * 本システムでは仮実装とするため全てControllerに収めている
 */
class TokenController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // 期限切れトークンを消す
        Artisan::call('sanctum:prune-expired', [
            '--hours' => 24
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::select(['user_id', 'password', 'roles'])
            ->where('email', $request->email)
            ->where('user_status_type', 'user_status')
            ->where('user_status', 'Active')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return response()->json([
            'token' => $user->createToken(
                'bizdevforge',
                explode(',', $user->roles),
                now()->addDay()
            )->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // 認証ユーザーのトークンを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json([], 201);
    }
}
