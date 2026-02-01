<?php

namespace Nadi\CodeIgniter\Handler;

use Nadi\CodeIgniter\Actions\ExceptionContext;
use Nadi\CodeIgniter\Actions\ExtractTags;
use Nadi\CodeIgniter\Data\ExceptionEntry;
use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use Nadi\Data\Type;
use Throwable;

class HandleExceptionEvent extends Base
{
    public function handle(Throwable $exception): void
    {
        $trace = array_map(function ($item) {
            return array_intersect_key($item, array_flip(['file', 'line']));
        }, $exception->getTrace());

        $otelAttributes = OpenTelemetrySemanticConventions::exceptionAttributes($exception);
        $userAttributes = OpenTelemetrySemanticConventions::userAttributes();
        $sessionAttributes = OpenTelemetrySemanticConventions::sessionAttributes();
        $otelData = array_merge($otelAttributes, $userAttributes, $sessionAttributes);

        $entryData = [
            'class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $trace,
            'line_preview' => ExceptionContext::get($exception),
            'otel' => $otelData,
        ];

        if (isset($_SERVER['REQUEST_URI'])) {
            $httpAttributes = OpenTelemetrySemanticConventions::httpAttributesFromGlobals();
            $entryData['otel'] = array_merge($entryData['otel'], $httpAttributes);
        }

        $this->store(
            ExceptionEntry::make(
                $exception,
                Type::EXCEPTION,
                $entryData
            )->setHashFamily(
                $this->hash(
                    get_class($exception).
                    $exception->getFile().
                    $exception->getLine().
                    $exception->getMessage().
                    date('Y-m-d')
                )
            )->tags($this->tags($exception))->toArray()
        );
    }

    protected function tags(Throwable $exception): array
    {
        $tags = ExtractTags::from($exception);

        $tags[] = OpenTelemetrySemanticConventions::EXCEPTION_TYPE.':'.get_class($exception);
        $tags[] = OpenTelemetrySemanticConventions::ERROR_TYPE.':'.get_class($exception);

        return $tags;
    }
}
