<?php

namespace App\Services;

use App\Models\User;
use App\Http\DTO\RegistrationDTO;
use Illuminate\Support\Facades\Hash;

class UserRegistrationService
{
    public function registerUser(RegistrationDTO $registration_DTO)
    {
        $user = User::create([
            'name' => $registration_DTO->name,
            'last_name' => $registration_DTO->last_name,
            'email' => $registration_DTO->email,
            'password' => Hash::make($registration_DTO->password),
        ]);

        return $user->createToken('auth_token')->plainTextToken;
    }
}
