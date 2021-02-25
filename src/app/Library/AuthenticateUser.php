<?php

namespace YllwDigital\YllwAuth\app\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

trait AuthenticateUser {
    /**
     * Handle a login request to the application
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request) {
        $this->validateLogin($request);

        if(!$this->attemptLogin($request)) {
            return $this->returnLoginError($request);
        }
        
        $this->emailVerified();

        return $this->returnLoginResponse($request);
    }

    /**
     * Validate the user login request
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request) {
        $request->validate($this->validations());
    }

    /**
     * Check if the user's email is verified
     * 
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function emailVerified() {
        if(config('yllwauth.email_verification') &&
        !$this->guard()->user()->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                $this->username() => config('yllwauth.email_verification_error')
            ]);
        }
    }

    /**
     * Returns the validation parameters
     * 
     * @return array
     */
    protected function validations() {
        $validations = [
            $this->username() => ['required'],
            $this->password() => ['required']
        ];

        if($this->username() == 'email') {
            $validations[$this->username()][] = 'email';
        }

        return $validations;
    }

    /**
     * Returns an exception if invalid email/password combination
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function returnLoginError(Request $request) {
        throw ValidationException::withMessages([
            $this->username()  => [config('yllwauth.login_error')]
        ]);
    }

    /**
     * Returns successful login response
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response\JsonResponse
     */
    protected function returnLoginResponse(Request $request) {
        $token = $this->createToken();

        return response()->json([
            config('yllwauth.token_key') => $token
        ], 200);
    }

    /**
     * Attempt to log the user into the application
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request) {
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }

    /**
     * Get the needed authorization credentials from the request
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request) {
        return $request->only(
            $this->username(), 
            $this->password()
        );
    }

    /**
     * Get the user's new authentication token
     * 
     * @return string
     */
    protected function createToken() {
        return $this->guard()->user()->createToken(
            config('yllwauth.token_name')
        )->accessToken;
    }

    /**
     * Get the guard to tbe used duringauthentication
     * 
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard() {
        return Auth::guard();
    }

    /**
     * Return the username field
     * 
     * @return string
     */
    protected function username() {
        return config('yllwauth.username');
    }

    /**
     * Return the password field
     * 
     * @return string
     */
    protected function password() {
        return config('yllwauth.password');
    }
}