<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Denylist Path
    |--------------------------------------------------------------------------
    |
    | Relative to base_path(). `import` it from your Caddyfile site block. The
    | file is written to a temp file and renamed over the target, so FrankenPHP
    | never reads a half-written list, and only when its content changed.
    |
    */
    'path' => env('SCANNER_GUARD_FRANKENPHP_PATH', 'storage/app/scanner-guard/denylist.caddy'),

    /*
    |--------------------------------------------------------------------------
    | Reload Command
    |--------------------------------------------------------------------------
    |
    | Leave null when FrankenPHP runs with --watch (recommended). Otherwise e.g.
    | "pkill -USR1 -x frankenphp": the server re-reads its Caddyfile with its
    | own environment. Avoid "frankenphp reload": it parses the Caddyfile with
    | the caller's environment (cron's is almost empty). Runs only when the file
    | content changed, never from a web request; a failure is logged.
    |
    */
    'reload_command' => env('SCANNER_GUARD_FRANKENPHP_RELOAD_COMMAND'),

];
