<?php

namespace Nadi\CodeIgniter\Data;

use Nadi\CodeIgniter\Concerns\InteractsWithMetric;
use Nadi\Data\ExceptionEntry as DataExceptionEntry;

class ExceptionEntry extends DataExceptionEntry
{
    use InteractsWithMetric;

    public function __construct($exception, $type, array $content)
    {
        parent::__construct($exception, $type, $content);

        $this->registerMetrics();
    }
}
