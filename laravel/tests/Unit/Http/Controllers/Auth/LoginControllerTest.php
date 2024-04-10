<?php

namespace Tests\Unit\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Requests\LoginRequest;
use App\Services\UserLoginService;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test user login with valid credentials.
     * @return void
     */
    public function test_user_login_with_valid_credentials()
    {
        $user_data = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);
        $login_request = Mockery::mock(LoginRequest::class);
        $login_request->shouldReceive('only')->once()->andReturn(['email' => $user_data['email'], 'password' => 'password123']);

        $user_login_service = Mockery::mock(UserLoginService::class);
        $user_login_service->shouldReceive('authenticate')->once()->andReturn($user_data);

        $controller = new LoginController($user_login_service);
        $response = $controller->login($login_request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $response_data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $response_data);
        $this->assertArrayHasKey('user', $response_data['data']);
        $this->assertEquals($user_data['name'], $response_data['data']['user']['name']);
        $this->assertEquals($user_data['email'], $response_data['data']['user']['email']);
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function test_user_login_with_invalid_credentials()
    {
        $login_request = Mockery::mock(LoginRequest::class);
        $login_request->shouldReceive('only')->once()->andReturn(['email' => 'invalid@example.com', 'password' => 'wrongpassword']);

        $user_login_service = Mockery::mock(UserLoginService::class);
        $user_login_service->shouldReceive('authenticate')->once()->andReturn(null);

        $controller = new LoginController($user_login_service);
        $response = $controller->login($login_request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->status());
        $response_data = $response->getData(true);
        $this->assertEquals('Invalid credentials', $response_data['message']);
    }

}
