<?php

namespace YllwDigital\YllwAuth\Http\Traits;

use Hash;

trait YllwAuthenticatable {
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
}