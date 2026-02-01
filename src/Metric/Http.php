<?php

namespace Nadi\CodeIgniter\Metric;

use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use Nadi\Metric\Base;

class Http extends Base
{
    public function metrics(): array
    {
        if (is_cli() || ! isset($_SERVER['REQUEST_URI'])) {
            return [];
        }

        return OpenTelemetrySemanticConventions::httpAttributesFromGlobals();
    }
}
