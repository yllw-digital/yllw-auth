<?php

namespace YllwDigital\YllwAuth\app\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

trait ResetPassword {
    /**
     * Request a password reset
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function request(Request $request) {
        $this->resetRoutesExist();

        $this->validateRequest($request);

        $status = Password::sendResetLink(
            $request->only(config('yllwauth.username'))
        );

        if($status === Password::RESET_LINK_SENT) {
            return $this->returnSuccess();
        }

        return $this->returnError();
    }

    /**
     * Reset user password
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request) {
        $this->validateReset($request);

        $status = Password::reset(
            $request->only(
                config('yllwauth.username'),
                'password',
                'password_confirmation',
                'token'
            ),
            function($user, $password) use ($request) {
                $user->forceFill([
                    'password' => $password
                ])->save();
            }
        );

        if($status === Password::PASSWORD_RESET) {
            return $this->returnSuccess();
        }

        return $this->returnError();
    }

    /**
     * Return success message
     * 
     * @param \Illuminate\Http\Request $request
     */
    protected function returnSuccess() {
        return response()->json(
            ['success' => true]
        , 200);
    }
    
    /**
     * Return error message
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function returnError() {
        throw ValidationException::withMessages([
            $this->username() => config('yllwauth.reset_password_error')
        ]);
    }
    
    /**
     * Check if the default laravel reset password routes exist
     * 
     * TODO: Create custom exception to throw if routes don't exist
     * 
     * @return void
     */
    protected function resetRoutesExist() {
        if(!Route::has("password.reset")) {
            abort(403, "Please make sure reset routes exist.");
        }
    }

    /**
     * Validating the user input 
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRequest(Request $request) {
        $validations[config('yllwauth.username')] = ['required'];

        if(config("yllwauth.username") == 'email') {
            $validations[config('yllwauth.username')][] = 'email';
        }

        $request->validate($validations);
    }

    /**
     * Validating the user reset input
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateReset(Request $request) {
        $validations = [
            'token'              => 'required',
            'password'           => 'required|min:8|confirmed',
            config('yllwauth.username') => ['required']
        ];

        if(config("yllwauth.username") == 'email') {
            $validations[config('yllwauth.username')][] = 'email';
        }

        $request->validate($validations);
    }
}