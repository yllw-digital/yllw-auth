<?php

namespace YllwDigital\YllwAuth\Http\Controllers;

use Auth;
use Illuminate\Validation\ValidationException;
use YllwDigital\YllwAuth\Http\Requests\LoginRequest;

class LoginController extends Controller {
    protected $userModel;
    
    public function __construct() {
        $this->userModel = config('auth.providers.users.model');
    }

    public function login(LoginRequest $request) {
        $attempt = Auth::attempt([
            'email'    => request('email'),
            'password' => request('password')
        ]);
        
        if($attempt) {
            return response()->json([
                'token' => auth()->user()->createToken("Yllw Authentication Token")->accessToken
            ], 200);
        }

        throw ValidationException::withMessages([
            'email' => ["Invalid email/password combination"]
        ]);
    }
}