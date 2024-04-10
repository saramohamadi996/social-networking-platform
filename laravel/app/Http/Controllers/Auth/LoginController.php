<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\ResponseGenerator;
use App\Http\Requests\LoginRequest;
use App\Services\UserLoginService;
use Illuminate\Http\JsonResponse;
use App\Http\DTO\UserDTO;

class LoginController extends Controller
{
    /**
     * User login service instance.
     * @var UserLoginService
     */
    private UserLoginService $user_login_service;

    /**
     * LoginController constructor.
     * @param UserLoginService $user_login_service
     */
    public function __construct(UserLoginService $user_login_service)
    {
        $this->user_login_service = $user_login_service;
    }

    /**
     * Authenticate user and generate token.
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $user = $this->user_login_service->authenticate($credentials);
        if (!$user) {
            return ResponseGenerator::unauthorized('Invalid credentials');
        }

        $user_DTO = new UserDTO($user->name, $user->last_name, $user->email);
        return ResponseGenerator::success( 'User logged in successfully',['user' => $user_DTO]);
    }


}
