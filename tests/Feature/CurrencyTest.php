<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class CurrencyTest extends TestCase
{
    public function test_login_success()
    {
        $baseUrl = env('APP_URL') . '/login';
        $email = env('USER_EMAIL');
        $password = env('USER_PASSWORD');

        $response = $this->json('POST', $baseUrl, [
            'email' => $email,
            'password' => $password,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    public function test_login_fail()
    {
        $baseUrl = env('APP_URL') . '/login';
        $email = env('USER_EMAIL');
        $password = env('USER_PASSWORD') . 'fake';

        $response = $this->json('POST', $baseUrl, [
            'email' => $email,
            'password' => $password,
        ]);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'error',
            ]);
    }

    public function test_get_currency_auth_success()
    {
        $baseUrl = env('APP_URL') . '/currency/1';
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $response = $this->json('GET', $baseUrl, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response
            ->assertStatus(200);
    }

    public function test_get_currency_auth_fail()
    {
        $baseUrl = env('APP_URL') . '/currency/1';
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $response = $this->json('GET', $baseUrl, [], [
            'Authorization' => 'Bearer ' . $token . 'fake',
        ]);
        $response
            ->assertStatus(403);
    }

    public function test_get_currency_not_found()
    {
        $baseUrl = env('APP_URL') . '/currency/200';
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $response = $this->json('GET', $baseUrl, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response
            ->assertStatus(404);
    }
}
