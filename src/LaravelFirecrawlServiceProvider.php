<?php

namespace JPCaparas\LaravelFirecrawl;

use JPCaparas\LaravelFirecrawl\Commands\FirecrawlExtractCommand;
use JPCaparas\LaravelFirecrawl\Commands\FirecrawlExtractStatusCommand;
use JPCaparas\LaravelFirecrawl\Commands\LaravelFirecrawlCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelFirecrawlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-firecrawl')
            ->hasConfigFile()
            ->hasCommands([
                LaravelFirecrawlCommand::class,
                FirecrawlExtractCommand::class,
                FirecrawlExtractStatusCommand::class,
            ]);
    }
}
