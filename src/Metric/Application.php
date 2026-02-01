<?php

namespace Nadi\CodeIgniter\Metric;

use Nadi\Metric\Base;

class Application extends Base
{
    public function metrics(): array
    {
        $metrics = [
            'app.environment' => defined('ENVIRONMENT') ? ENVIRONMENT : 'production',
        ];

        if (! is_cli()) {
            if (defined('APPPATH')) {
                $metrics['app.path'] = APPPATH;
            }
        } else {
            $metrics['app.context'] = 'console';

            if (isset($_SERVER['argv'])) {
                $metrics['app.command'] = implode(' ', array_slice($_SERVER['argv'], 1));
            }
        }

        return $metrics;
    }
}
