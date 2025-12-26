<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Auth\ForgotPasswordRequest;
use App\Http\Requests\Public\Auth\LoginRequest;
use App\Http\Requests\Public\Auth\RegisterRequest;
use App\Http\Requests\Public\Auth\ResetPasswordRequest;
use App\Http\Requests\Public\Auth\VerifyCodeRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {}


    public function register(RegisterRequest $request)
    {
        $this->service->register($request->toDTO());
        return $this->successResponse();
    }


    public function login(LoginRequest $request)
    {
        $result = $this->service->login($request->toDTO());
        return $this->successResponse(['token' => $result]);
    }


    public function logout()
    {
        $this->service->logout(Auth::user());
        return $this->successResponse();
    }


    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->service->forgotPassword($request->toDTO());
        return $this->successResponse();
    }


    public function verify(VerifyCodeRequest $request)
    {
        $this->service->verify($request->toDTO());
        return $this->successResponse();
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->service->resetPassword($request->toDTO());
        return $this->successResponse();
    }
}
