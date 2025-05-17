<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\AuthUseCase;
use Illuminate\Http\Request;

class AuthController extends Controller
{
   
    public function __construct(
        protected AuthUseCase $authUseCase
    ) {}

    public function login(Request $request)
    {
        return response()->json($this->authUseCase->login($request));
    }

    public function register(Request $request)
    {
        return response()->json($this->authUseCase->register($request), 201);
    }
    

    public function me(Request $request)
    {
        return response()->json($this->authUseCase->me($request));
    }

    public function logout(Request $request)
    {
        return response()->json($this->authUseCase->logout($request));
    }
}