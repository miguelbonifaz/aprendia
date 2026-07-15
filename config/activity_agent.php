<?php

return [
    'driver' => env('ACTIVITY_AGENT_DRIVER', 'codex'),

    'codex' => [
        'binary' => env('ACTIVITY_AGENT_CODEX_BINARY', 'codex'),
        'timeout' => (int) env('ACTIVITY_AGENT_CODEX_TIMEOUT', 120),
    ],
];
