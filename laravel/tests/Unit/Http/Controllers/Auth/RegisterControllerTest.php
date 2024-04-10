<?php

namespace Tests\Unit\Http\Controllers\Auth;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Requests\RegistrationRequest;
use App\Services\UserRegistrationService;
use App\Http\DTO\RegistrationDTO;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use Mockery;

class RegisterControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Test registering a user successfully
     * @return void
     */
    public function test_it_registers_a_user_successfully()
    {
        $request_data = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];
        $registration_request = Mockery::mock(RegistrationRequest::class);
        $registration_request->shouldReceive('validated')->once()->andReturn($request_data);
        $token = 'some_token';
        $user_registration_service = Mockery::mock(UserRegistrationService::class);
        $user_registration_service->shouldReceive('registerUser')->once()
            ->withArgs([Mockery::type(RegistrationDTO::class)])
            ->andReturn($token);

        $register_controller = new RegisterController($user_registration_service);

        $response = $register_controller->register($registration_request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $response_data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $response_data);
        $this->assertArrayHasKey('user', $response_data['data']);
        $this->assertEquals($request_data['name'], $response_data['data']['user']['name']);
        $this->assertEquals($request_data['last_name'], $response_data['data']['user']['last_name']);
        $this->assertEquals($request_data['email'], $response_data['data']['user']['email']);
        $this->assertEquals('User registered successfully', $response_data['message']);
    }
}
