<?php

// config for Skeylup/LaravelSmsDev
return [
    /*
    |--------------------------------------------------------------------------
    | SMS Dev Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour le package SMS Dev qui intercepte les SMS
    | pour le développement et le débogage.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Détermine si le package SMS Dev est activé. Généralement, vous voulez
    | l'activer uniquement en développement et pré-production.
    |
    */
    'enabled' => env('SMS_DEV_ENABLED', app()->environment(['local', 'testing', 'staging'])),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des routes pour l'interface web SMS Dev.
    |
    */
    'route' => [
        'prefix' => env('SMS_DEV_ROUTE_PREFIX', 'sms-dev'),
        'middleware' => ['web'],
        'name' => 'sms-dev.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-cleanup
    |--------------------------------------------------------------------------
    |
    | Configuration pour le nettoyage automatique des anciens SMS.
    |
    */
    'cleanup' => [
        'enabled' => env('SMS_DEV_CLEANUP_ENABLED', true),
        'days' => env('SMS_DEV_CLEANUP_DAYS', 30), // Supprimer les SMS de plus de 30 jours
    ],

    /*
    |--------------------------------------------------------------------------
    | Default From Number
    |--------------------------------------------------------------------------
    |
    | Default sender number for intercepted SMS messages.
    |
    */
    'default_from' => env('SMS_DEV_DEFAULT_FROM', 'SMS-DEV'),

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | SMS Dev authorization configuration. By default, SMS Dev is only
    | accessible in local environments. You may use the gate method to
    | define authorization logic for production environments.
    |
    */
    'gate' => env('SMS_DEV_GATE', 'ViewSms'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware stack for SMS Dev routes. The authorization middleware
    | is only applied in production environments.
    |
    */
    'middleware' => array_filter([
        'web',
        app()->environment('production') ? 'sms-dev-auth' : null,
    ]),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Number of items per page in the web interface.
    |
    */
    'per_page' => env('SMS_DEV_PER_PAGE', 20),
];
