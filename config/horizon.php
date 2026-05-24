<?php

use Illuminate\Support\Str;

return [
    'name'   => env('HORIZON_NAME', 'Veltrixo'),
    'domain' => env('HORIZON_DOMAIN'),
    'path'   => env('HORIZON_PATH', 'horizon'),
    'use'    => 'default',

    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'),

    'middleware' => ['web'],

    'waits' => [
        'redis:critical'      => 3,
        'redis:deliveries'    => 10,
        'redis:wallets'       => 10,
        'redis:notifications' => 30,
        'redis:invoices'      => 30,
        'redis:default'       => 60,
        'redis:analytics'     => 120,
        'redis:cleanup'       => 300,
    ],

    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 60,
        'recent_failed' => 10080,
        'failed'        => 10080,
        'monitored'     => 10080,
    ],

    'silenced' => [],

    'metrics' => [
        'trim_snapshots' => [
            'job'   => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => false,
    'memory_limit'     => 128,

    'defaults' => [
        // ── Critical: auth, OTP, real-time ops ───────────────────────────
        'supervisor-critical' => [
            'connection'           => 'redis',
            'queue'                => ['critical'],
            'balance'              => 'auto',
            'autoScalingStrategy'  => 'time',
            'maxProcesses'         => 5,
            'minProcesses'         => 1,
            'memory'               => 128,
            'tries'                => 3,
            'timeout'              => 30,
            'nice'                 => -5,
        ],

        // ── Deliveries: generation + assignment ───────────────────────────
        'supervisor-deliveries' => [
            'connection'          => 'redis',
            'queue'               => ['deliveries'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 8,
            'minProcesses'        => 2,
            'memory'              => 128,
            'tries'               => 3,
            'timeout'             => 300,
            'nice'                => 0,
        ],

        // ── Wallets: deductions, recharges, refunds ───────────────────────
        'supervisor-wallets' => [
            'connection'          => 'redis',
            'queue'               => ['wallets'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 5,
            'minProcesses'        => 1,
            'memory'              => 128,
            'tries'               => 3,
            'timeout'             => 60,
            'nice'                => 0,
        ],

        // ── Notifications: push, SMS, email ──────────────────────────────
        'supervisor-notifications' => [
            'connection'          => 'redis',
            'queue'               => ['notifications'],
            'balance'             => 'simple',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 5,
            'minProcesses'        => 1,
            'memory'              => 128,
            'tries'               => 3,
            'timeout'             => 60,
            'nice'                => 5,
        ],

        // ── Invoices: PDF generation, email dispatch ──────────────────────
        'supervisor-invoices' => [
            'connection'          => 'redis',
            'queue'               => ['invoices'],
            'balance'             => 'simple',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 3,
            'minProcesses'        => 1,
            'memory'              => 256,
            'tries'               => 3,
            'timeout'             => 120,
            'nice'                => 5,
        ],

        // ── Analytics: slow background aggregation ────────────────────────
        'supervisor-analytics' => [
            'connection'          => 'redis',
            'queue'               => ['analytics'],
            'balance'             => 'simple',
            'autoScalingStrategy' => 'size',
            'maxProcesses'        => 2,
            'minProcesses'        => 1,
            'memory'              => 256,
            'tries'               => 2,
            'timeout'             => 600,
            'nice'                => 10,
        ],

        // ── Default: catch-all ────────────────────────────────────────────
        'supervisor-default' => [
            'connection'          => 'redis',
            'queue'               => ['default'],
            'balance'             => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'        => 3,
            'minProcesses'        => 1,
            'memory'              => 128,
            'tries'               => 3,
            'timeout'             => 90,
            'nice'                => 5,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-critical' => [
                'maxProcesses'       => 10,
                'balanceMaxShift'    => 2,
                'balanceCooldown'    => 2,
            ],
            'supervisor-deliveries' => [
                'maxProcesses'       => 20,
                'balanceMaxShift'    => 5,
                'balanceCooldown'    => 3,
            ],
            'supervisor-wallets' => [
                'maxProcesses'       => 10,
                'balanceMaxShift'    => 2,
                'balanceCooldown'    => 3,
            ],
            'supervisor-notifications' => [
                'maxProcesses'       => 10,
                'balanceMaxShift'    => 3,
                'balanceCooldown'    => 3,
            ],
            'supervisor-invoices' => [
                'maxProcesses'       => 5,
            ],
            'supervisor-analytics' => [
                'maxProcesses'       => 3,
            ],
            'supervisor-default' => [
                'maxProcesses'       => 5,
            ],
        ],

        'local' => [
            'supervisor-critical'      => ['maxProcesses' => 2],
            'supervisor-deliveries'    => ['maxProcesses' => 2],
            'supervisor-wallets'       => ['maxProcesses' => 2],
            'supervisor-notifications' => ['maxProcesses' => 2],
            'supervisor-invoices'      => ['maxProcesses' => 1],
            'supervisor-analytics'     => ['maxProcesses' => 1],
            'supervisor-default'       => ['maxProcesses' => 2],
        ],
    ],
];
