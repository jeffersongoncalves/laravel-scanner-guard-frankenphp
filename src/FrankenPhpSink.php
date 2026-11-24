<?php

declare(strict_types=1);

namespace JeffersonGoncalves\ScannerGuardFrankenPhp;

use JeffersonGoncalves\ScannerGuardCaddy\CaddySink;

/**
 * FrankenPHP is Caddy, so the file is the Caddy sink's. What differs is the
 * reload: PHP runs inside the server being reloaded.
 */
class FrankenPhpSink extends CaddySink
{
    protected string $config = 'scanner-guard-frankenphp';

    public function sync(array $entries): void
    {
        // `frankenphp reload` waits for running requests, so from a request (a
        // sync queue) it would wait for itself. The scheduled sync, on the CLI,
        // writes and reloads within a minute. Skipping the write too matters:
        // with the file already up to date that run would not reload.
        if ($this->insideServer() && (string) config($this->config.'.reload_command') !== '') {
            return;
        }

        parent::sync($entries);
    }

    protected function insideServer(): bool
    {
        return PHP_SAPI === 'frankenphp';
    }
}
