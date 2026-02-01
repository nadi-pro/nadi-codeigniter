<?php

namespace Nadi\CodeIgniter\Metric;

use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use Nadi\Metric\Base;

class Framework extends Base
{
    public function metrics(): array
    {
        $config = function_exists('config') ? config('Nadi') : null;

        return [
            'framework.name' => 'codeigniter',
            'framework.version' => defined('CI_VERSION') ? CI_VERSION : 'unknown',
            OpenTelemetrySemanticConventions::SERVICE_NAME => $config->connections['opentelemetry']['service_name'] ?? 'codeigniter-app',
            OpenTelemetrySemanticConventions::SERVICE_VERSION => $config->connections['opentelemetry']['service_version'] ?? '1.0.0',
            OpenTelemetrySemanticConventions::DEPLOYMENT_ENVIRONMENT => $config->connections['opentelemetry']['deployment_environment'] ?? (defined('ENVIRONMENT') ? ENVIRONMENT : 'production'),
        ];
    }
}
