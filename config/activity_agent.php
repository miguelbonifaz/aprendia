<?php

return [
    'driver' => env('ACTIVITY_AGENT_DRIVER', 'codex'),

    'codex' => [
        'binary' => env('ACTIVITY_AGENT_CODEX_BINARY', 'codex'),
        'timeout' => (int) env('ACTIVITY_AGENT_CODEX_TIMEOUT', 120),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-5.6-luna'),
        'reasoning_effort' => env('OPENAI_REASONING_EFFORT', 'max'),
        'timeout' => (int) env('ACTIVITY_AGENT_OPENAI_TIMEOUT', 220),
        'connect_timeout' => (int) env('ACTIVITY_AGENT_OPENAI_CONNECT_TIMEOUT', 10),
    ],
];
