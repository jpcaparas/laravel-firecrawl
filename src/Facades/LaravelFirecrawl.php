<?php

namespace JPCaparas\LaravelFirecrawl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JPCaparas\LaravelFirecrawl\LaravelFirecrawl
 */
class LaravelFirecrawl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JPCaparas\LaravelFirecrawl\LaravelFirecrawl::class;
    }
}
