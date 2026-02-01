<?php

namespace Nadi\CodeIgniter\Config;

use CodeIgniter\Config\BaseConfig;

class Nadi extends BaseConfig
{
    public bool $enabled = true;

    public string $driver = 'log';

    public array $connections = [
        'log' => [
            'path' => WRITEPATH.'nadi',
        ],
        'http' => [
            'apiKey' => '',
            'appKey' => '',
            'endpoint' => 'https://api.nadi.pro',
            'version' => 'v1',
        ],
        'opentelemetry' => [
            'endpoint' => 'http://localhost:4318',
            'service_name' => 'codeigniter-app',
            'service_version' => '1.0.0',
            'deployment_environment' => 'production',
            'suppress_errors' => true,
        ],
    ];

    public array $query = [
        'slow_threshold' => 500,
    ];

    public array $http = [
        'hidden_request_headers' => [
            'authorization',
            'php-auth-pw',
        ],
        'hidden_parameters' => [
            'password',
            'password_confirmation',
        ],
        'ignored_status_codes' => [
            100, 101, 102, 103,
            200, 201, 202, 203, 204, 205, 206, 207,
            300, 302, 303, 304, 305, 306, 307, 308,
        ],
    ];

    public array $sampling = [
        'strategy' => 'fixed_rate',
        'config' => [
            'sampling_rate' => 0.1,
            'base_rate' => 0.05,
            'load_factor' => 1.0,
            'interval_seconds' => 60,
        ],
        'strategies' => [
            'dynamic_rate' => \Nadi\Sampling\DynamicRateSampling::class,
            'fixed_rate' => \Nadi\Sampling\FixedRateSampling::class,
            'interval' => \Nadi\Sampling\IntervalSampling::class,
            'peak_load' => \Nadi\Sampling\PeakLoadSampling::class,
        ],
    ];
}
