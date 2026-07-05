<?php

return [
    'apps' => [
        [
            'app_id' => env('REVERB_APP_ID', 'avaradesa'),
            'key' => env('REVERB_APP_KEY', 'avaradesa-key'),
            'secret' => env('REVERB_APP_SECRET', 'avaradesa-secret'),
            'host' => env('REVERB_HOST', 'localhost'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'options' => [
                'tls' => [],
            ],
        ],
    ],
    'scaling' => [
        'enabled' => false,
        'channel' => 'reverb',
        'server' => [
            'url' => env('REVERB_SERVER_URL', 'http://localhost:8080'),
            'host' => env('REVERB_SERVER_HOST', 'localhost'),
            'port' => env('REVERB_SERVER_PORT', 8080),
            'hostname' => env('REVERB_SERVER_HOSTNAME', 'localhost'),
            'options' => [],
        ],
    ],
    'pulse_ingest_interval' => env('REVERB_PULSE_INGEST_INTERVAL', 15),
    'telescope_ingest_interval' => env('REVERB_TELESCOPE_INGEST_INTERVAL', 15),
];
