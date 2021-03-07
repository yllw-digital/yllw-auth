<?php

namespace YllwDigital\YllwAuth\Tests\Unit;

use YllwDigital\YllwAuth\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function users_table_exists() {
        $this->assertTrue(Schema::hasTable('users'));
    }

    /** @test */
    public function users_table_contains_email_field() {
        $this->assertTrue(Schema::hasColumn('users', 'email'));
    }

    /** @test */
    public function users_table_contains_password_field() {
        $this->assertTrue(Schema::hasColumn('users', 'password'));
    }
}
