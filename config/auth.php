<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default guard and password reset options
    | for your application. You may change these defaults as needed.
    |
    */

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Here you may define every authentication guard for your application.
    | We've added both session-based (web) and token-based (Sanctum) guards
    | for users, admins, and companies.
    |
    */

    'guards' => [
        // Users
        'web'       => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'api'       => [
            'driver'   => 'sanctum',
            'provider' => 'users',
        ],

        // Admins
        'admin'     => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
        'admin-api' => [
            'driver'   => 'sanctum',
            'provider' => 'admins',
        ],

        // Companies
        'company'     => [
            'driver'   => 'session',
            'provider' => 'companies',
        ],
        'company-api' => [
            'driver'   => 'sanctum',
            'provider' => 'companies',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how
    | users are actually retrieved from your database or other storage.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],

        'companies' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Company::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have
    | more than one user table or model in the application.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'admin_password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'companies' => [
            'provider' => 'companies',
            'table'    => 'company_password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password.
    |
    */

    'password_timeout' => 10800,

];
