<?php

namespace Skeylup\LaravelSmsDev;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Skeylup\LaravelSmsDev\Channels\SmsDevChannel;
use Skeylup\LaravelSmsDev\Commands\LaravelSmsDevCommand;
use Skeylup\LaravelSmsDev\Http\Middleware\Authorize;

class LaravelSmsDevServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-sms-dev')
            ->hasConfigFile('sms-dev')
            ->hasViews('sms-dev')
            ->hasMigration('create_sms_logs_table')
            ->hasRoute('web')
            ->hasCommand(LaravelSmsDevCommand::class);
    }

    public function packageBooted(): void
    {
        // Register SMS Dev notification channel
        $this->app->make(ChannelManager::class)->extend('sms-dev', function () {
            return new SmsDevChannel();
        });

        // Register middleware
        $this->app['router']->aliasMiddleware('sms-dev-auth', Authorize::class);
    }
}
