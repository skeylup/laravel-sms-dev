<?php

namespace Skeylup\LaravelSmsDev\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Skeylup\LaravelSmsDev\LaravelSmsDev
 */
class LaravelSmsDev extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Skeylup\LaravelSmsDev\LaravelSmsDev::class;
    }
}
