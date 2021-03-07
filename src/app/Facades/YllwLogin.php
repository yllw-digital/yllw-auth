<?php

namespace YllwDigital\YllwAuth\app\Facades;

use Illuminate\Support\Facades\Facade;

class YllwLogin extends Facade {
    protected static function getFacadeAccessor() { return 'yllwLogin'; }
}