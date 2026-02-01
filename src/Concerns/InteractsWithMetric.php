<?php

namespace Nadi\CodeIgniter\Concerns;

use Nadi\CodeIgniter\Metric\Application;
use Nadi\CodeIgniter\Metric\Framework;
use Nadi\CodeIgniter\Metric\Http;
use Nadi\CodeIgniter\Metric\Network;

trait InteractsWithMetric
{
    public function registerMetrics(): void
    {
        if (method_exists($this, 'addMetric')) {
            $this->addMetric(new Http);
            $this->addMetric(new Framework);
            $this->addMetric(new Application);
            $this->addMetric(new Network);
        }
    }
}
