<?php

declare(strict_types=1);

namespace JeffersonGoncalves\ScannerGuardFrankenPhp;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ScannerGuardFrankenPhpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-scanner-guard-frankenphp')
            ->hasConfigFile('scanner-guard-frankenphp');
    }
}
