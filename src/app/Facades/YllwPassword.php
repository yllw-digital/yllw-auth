<?php

namespace YllwDigital\YllwAuth\app\Facades;

use Illuminate\Support\Facades\Facade;

class YllwPassword extends Facade {
    protected static function getFacadeAccessor() { return 'yllwPassword'; }
}