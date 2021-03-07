<?php

namespace YllwDigital\YllwAuth\Tests\Unit;

use Illuminate\Support\Facades\Route;
use YllwDigital\YllwAuth\Tests\TestCase;

class RouteTest extends TestCase {
    /** @test */
    public function login_route_exists() {
        $this->assertTrue(Route::has('login'));
    }
}
