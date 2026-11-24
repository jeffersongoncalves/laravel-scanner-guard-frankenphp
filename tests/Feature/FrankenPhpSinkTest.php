<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use JeffersonGoncalves\ScannerGuardFrankenPhp\FrankenPhpSink;

beforeEach(function () {
    config(['scanner-guard-frankenphp.path' => 'storage/app/scanner-guard/denylist-test.caddy']);
    $this->file = storage_path('app/scanner-guard/denylist-test.caddy');
    $this->entries = [['ip' => '10.20.20.23', 'reason' => 'static', 'expires_at' => null]];
    $this->insideServer = new class extends FrankenPhpSink
    {
        protected function insideServer(): bool
        {
            return true;
        }
    };
});

afterEach(function () {
    File::deleteDirectory(storage_path('app/scanner-guard'));
});

it('runs its own reload command only when the file content changed', function () {
    Process::fake();
    config(['scanner-guard-frankenphp.reload_command' => 'pkill -USR1 -x frankenphp']);

    (new FrankenPhpSink)->sync($this->entries);
    (new FrankenPhpSink)->sync($this->entries);
    (new FrankenPhpSink)->sync([]);

    Process::assertRanTimes('pkill -USR1 -x frankenphp', 2);
});

it('leaves the write and the reload to the CLI when inside a FrankenPHP request', function () {
    Process::fake();
    config(['scanner-guard-frankenphp.reload_command' => 'frankenphp reload']);

    $this->insideServer->sync($this->entries);

    expect(File::exists($this->file))->toBeFalse();
    Process::assertNothingRan();

    (new FrankenPhpSink)->sync($this->entries);

    Process::assertRanTimes('frankenphp reload', 1);
});

it('still writes inside a request when there is nothing to run (--watch)', function () {
    $this->insideServer->sync($this->entries);

    expect(File::get($this->file))->toContain('client_ip 10.20.20.23 # static');
});

it('is driven by scanner-guard:sync-denylist', function () {
    config([
        'scanner-guard.driver' => 'http', // no database needed: bans come from the (empty) last pull
        'scanner-guard.sinks' => [FrankenPhpSink::class],
        'scanner-guard.ip_blocklist' => ['185.177.72.69', 'not-an-ip'],
    ]);

    $this->artisan('scanner-guard:sync-denylist')->assertSuccessful();

    expect(File::get($this->file))->toBe("@scanner_guard {\n\tclient_ip 185.177.72.69 # static\n}\nabort @scanner_guard\n");
});
