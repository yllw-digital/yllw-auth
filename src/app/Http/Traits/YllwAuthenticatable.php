<?php

namespace YllwDigital\YllwAuth\app\Http\Traits;

use Hash;

trait YllwAuthenticatable {
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
}