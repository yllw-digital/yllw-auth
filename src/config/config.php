<?php

return [
    /**
     * Username field that we will use to authenticate 
     */
    'username' => env("YLLWAUTH_USERNAME", 'email'),

    /**
     * Password field that we will use to authenticate
     */
    'password' => env("YLLWAUTH_PASSWORD", 'password'),
    
    /**
     * Default token key name
     */
    'token_key' => env("YLLWAUTH_TOKEN_KEY", 'token'),

    /**
     * Token name
     */
    'token_name' => env("YLLWAUTH_TOKEN_NAME", 'Yllw Authentication Token'),
    
    /**
     * User model
     */
    'user_model' => \App\Models\User::class,

    /**
     * Whether or not users should verify their emails
     */
    'email_verification' => env("YLLWAUTH_EMAIL_VERIFICATION", true),

    /**
     * Type of verification
     * values: link
     * 
     * TODO: Add "code" option to send code by email
     */
    'email_verification_type' => env("YLLWAUTH_EMAIL_VERIFICATION_TYPE", 'link'),

    
    /**
     * Default login error if invalid combination
     */
    'login_error' => env("YLLWAUTH_LOGIN_ERROR", 'Invalid email/password combination.'),
    
    /**
     * Default missing email verification error
     */
    'email_verification_error' => env("YLLWAUTH_EMAIL_VERIFICATION_ERROR", "Please make sure you verify your email before logging in."),

    /**
     * Default reset password error
     */
    'password_reset_error' => env("YLLWAUTH_PASSWORD_RESET_ERROR", "Something went wrong while resetting your password.")
];