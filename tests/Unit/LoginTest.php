<?php

namespace YllwDigital\YllwAuth\Tests\Unit;

use YllwDigital\YllwAuth\Tests\Models\User;
use YllwDigital\YllwAuth\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class LoginTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function login_fails_without_email() {
        $response = $this->postJson(route('login'), [
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['email']]);
    }

    /** @test */
    public function login_fails_without_password() {
        $response = $this->postJson(route('login'), [
            'email' => 'test@email.com'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['password']]);
    }
}
