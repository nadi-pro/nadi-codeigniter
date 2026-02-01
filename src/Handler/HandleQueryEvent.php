<?php

namespace Nadi\CodeIgniter\Handler;

use Nadi\CodeIgniter\Concerns\FetchesStackTrace;
use Nadi\CodeIgniter\Data\Entry;
use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use Nadi\Data\Type;

class HandleQueryEvent extends Base
{
    use FetchesStackTrace;

    public function handleQuery($query): void
    {
        $config = config('Nadi');
        $slowThreshold = $config->query['slow_threshold'] ?? 500;

        $time = $query->getDuration();

        if ($time <= $slowThreshold) {
            return;
        }

        $sql = (string) $query;
        $connectionName = 'default';

        $otelAttributes = OpenTelemetrySemanticConventions::databaseAttributes($connectionName, $sql, $time);
        $userAttributes = OpenTelemetrySemanticConventions::userAttributes();
        $sessionAttributes = OpenTelemetrySemanticConventions::sessionAttributes();
        $otelData = array_merge($otelAttributes, $userAttributes, $sessionAttributes);

        if ($caller = $this->getCallerFromStackTrace()) {
            $otelData[OpenTelemetrySemanticConventions::CODE_FILEPATH] = $caller['file'];
            $otelData[OpenTelemetrySemanticConventions::CODE_LINENO] = $caller['line'];

            $entryData = [
                'connection' => $connectionName,
                'sql' => $sql,
                'time' => number_format($time, 2, '.', ''),
                'slow' => true,
                'file' => $caller['file'],
                'line' => $caller['line'],
                'otel' => $otelData,
            ];

            $this->store(
                Entry::make(Type::QUERY, $entryData)
                    ->setHashFamily($this->hash($sql.date('Y-m-d')))
                    ->tags($this->tags($sql, $connectionName))
                    ->toArray()
            );
        }
    }

    protected function tags(string $sql, string $connectionName): array
    {
        $tags = ['slow'];
        $tags[] = OpenTelemetrySemanticConventions::DB_CONNECTION_NAME.':'.$connectionName;

        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|TRUNCATE)\s+/i', $sql, $matches)) {
            $tags[] = OpenTelemetrySemanticConventions::DB_OPERATION.':'.strtoupper($matches[1]);
        }

        $tags[] = 'query.slow:true';

        return $tags;
    }
}
