<?php

namespace YllwDigital\YllwAuth\app\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

trait RegisterUser {
    /**
     * Additional registration fields
     * @var array
     */
    protected $additionalFields = [];

    /**
     * Register a user to the application
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request, array $additionalFields = []) {
        $this->additionalFields = $additionalFields;

        $this->verificationRoutesExist();

        $this->validateRegister($request);

        $user = $this->attemptRegister($request);
        
        $this->sendVerificationEmail($user);

        return $this->returnResponse($user->id);
    }

    /**
     * Sends a verification email to the user
     * 
     * @param $user
     * 
     * @return void;
     */
    protected function sendVerificationEmail($user) {
        if(config("yllwauth.email_verification")) {
            $user->sendEmailVerificationNotification();
        }
    }

    /**
     * Check if the default laravel verification routes exist
     * 
     * TODO: Create custom exception to throw if routes don't exist
     * TODO: Add resend code logic
     * 
     * @return void
     */
    protected function verificationRoutesExist() {
        if(config("yllwauth.email_verification") &&
            (!Route::has("verification.verify") || !Route::has("verification.resend"))) {
                abort(403, "Please make sure verification routes exist.");
            }
    }

    /**
     * Verify the user's email
     * 
     * TODO: Create custom exception to throw if it's not a valid signature
     * 
     * @param int $user_id
     * @param \Illuminate\Http\Request $request
     */
    public function verify(int $user_id, Request $request) {
        if(!$request->hasValidSignature()) {
            abort(403, "Invalid or Expired URL provided.");
        }

        $user = $this->userModel()->findOrFail($user_id);

        if(!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Return user ID
     * 
     * @param int $id
     * @return \Illuminate\Http\Response\JsonResponse
     */
    protected function returnResponse(int $id) {
        return response()->json(
            ['id' => $id]
        , 200);
    }

    /**
     * Register the user in the database
     * 
     * @param \Illuminate\Http\Request $request
     * @return integer
     */
    public function attemptRegister(Request $request) {
        $user = $this->userModel()->create(
            $this->createParameters($request)
        );

        return $user;
    }

    /**
     * Returns fields to be created in the database
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function createParameters(Request $request) {
        return $request->only(
            array_merge([
                    $this->username(),
                    $this->password(),
                ],
                $this->additionalCreateFields()
            )
        );
    }

    /**
     * Validate the user register request
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRegister(Request $request) {
        $request->validate($this->validations());
    }

    /**
     * Returns the validation parameters
     * 
     * @return array
     */
    protected function validations() {
        $validations = $this->additionalValidations();

        $validations[$this->username()] = [
            'required',
            'unique:' . $this->userTable() . ',' . $this->username()
        ];

        $validations[$this->password()] = ['required'];

        if($this->username() == 'email') {
            $validations[$this->username()][] = 'email';
        }

        return $validations;
    }

    /**
     * Returns the additional validations
     * 
     * @return array
     */
    protected function additionalValidations() {
        $validations = [];

        foreach($this->additionalFields as $fieldName => $validation) {
            $validations[$fieldName] = $validation['validations'];
        }

        return $validations;
    }

    /**
     * Returns additional create columns
     * 
     * @return array
     */
    protected function additionalCreateFields() {
        $columns = [];

        foreach($this->additionalFields as $fieldName => $validation) {
            if(isset($validation['create']) && $validation['create']) {
                $columns[] = $fieldName;
            }
        }

        return $columns;
    }

    /**
     * Get the user model
     * 
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function userModel() {
        return app(config('yllwauth.user_model'));
    }

    /**
     * Get the user table name
     * 
     * @return string
     */
    protected function userTable() {
        return $this->userModel()->getTable();
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