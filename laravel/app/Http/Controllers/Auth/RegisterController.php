<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Response\ResponseGenerator;
use App\Services\UserRegistrationService;
use Illuminate\Http\JsonResponse;
use App\Http\DTO\RegistrationDTO;
use App\Http\DTO\UserDTO;

class RegisterController extends Controller
{
    /**
     * User registration service instance.
     * @var UserRegistrationService
     */
    private UserRegistrationService $user_registration_service;

    /**
     * RegisterController constructor.
     * @param UserRegistrationService $user_registration_service
     */
    public function __construct(UserRegistrationService $user_registration_service)
    {
        $this->user_registration_service = $user_registration_service;
    }

    /**
     * Register a new user.
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        $request_data = $request->validated();
        $registration_DTO = new RegistrationDTO(...array_values($request_data));
        $this->user_registration_service->registerUser($registration_DTO);
        $user_DTO = new UserDTO(...array_values($request_data));
        return ResponseGenerator::success( 'User registered successfully', ['user' => $user_DTO]);
    }

}
