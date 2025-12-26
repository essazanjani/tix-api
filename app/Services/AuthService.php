<?php

namespace App\Services;

use App\DTOs\Public\Auth\ForgotPasswordDTO;
use App\DTOs\Public\Auth\LoginDTO;
use App\DTOs\Public\Auth\RegisterDTO;
use App\DTOs\Public\Auth\ResetPasswordDTO;
use App\DTOs\Public\Auth\VerifyCodeDTO;
use App\Events\ForgotPassword;
use App\Exceptions\CustomException;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(RegisterDTO $dto): void
    {
        User::query()->create((array)$dto);
    }


    public function login(LoginDTO $dto): string
    {
        $user = User::query()->where('email', $dto->email)->first();

        if (!Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages(['password' => trans('auth.password')]);
        }

        $expiration = $dto->remember_me ? now()->addWeek() : now()->addDay();
        return $user->createToken(config('auth.token_name'), expiresAt: $expiration)->plainTextToken;
    }


    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }


    public function forgotPassword(ForgotPasswordDTO $dto): void
    {
        $user = User::query()->where('email', $dto->email)->first();

        $hasActiveCode = PasswordResetCode::query()
            ->where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->exists();
        if ($hasActiveCode) {
            throw ValidationException::withMessages(['password' => trans('passwords.throttled')]);
        }

        $passwordResetCode = PasswordResetCode::query()
            ->create([
               'user_id' => $user->id,
                'code' => rand(10000, 99999),
                'expires_at' => now()->addMinutes(5)
            ]);

        event(new ForgotPassword($user, $passwordResetCode));
    }


    public function verify(VerifyCodeDTO $dto): void
    {
        $user = User::query()->where('email', $dto->email)->first();

        $passwordResetCode = PasswordResetCode::query()
            ->where('user_id', $user->id)
            ->where('code', $dto->code)
            ->where('expires_at', '>', now())
            ->first();
        if (!$passwordResetCode) {
            throw ValidationException::withMessages(['code' => trans('passwords.code')]);
        }

        $passwordResetCode->update([
            'verified_at' => now(),
            'expires_at' => now()->addMinutes(5)
        ]);
    }


    public function resetPassword(ResetPasswordDTO $dto): void
    {
        $user = User::query()->where('email', $dto->email)->first();

        $passwordResetCode = PasswordResetCode::query()
            ->where('user_id', $user->id)
            ->whereNotNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();
        if (!$passwordResetCode) {
            throw new CustomException();
        }

        $user->update(['password' => $dto->password]);
    }
}
