<?php

use Illuminate\Support\Facades\Route;
use Skeylup\LaravelSmsDev\Http\Controllers\SmsLogController;

/*
|--------------------------------------------------------------------------
| SMS Dev Routes
|--------------------------------------------------------------------------
|
| Routes pour l'interface de débogage SMS
|
*/

Route::prefix('sms-dev')
    ->name('sms-dev.')
    ->middleware(config('sms-dev.middleware', ['web', 'sms-dev-auth']))
    ->group(function () {

        // Liste des SMS avec actions (nouvelle interface mailbox)
        Route::get('/', [SmsLogController::class, 'index'])
            ->name('index');

        // Afficher un SMS spécifique (ancienne interface, gardée pour compatibilité)
        Route::get('/{smsLog}/show', [SmsLogController::class, 'show'])
            ->name('show');
    });
