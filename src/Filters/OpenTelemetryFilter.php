<?php

namespace Nadi\CodeIgniter\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\Context\Context;

class OpenTelemetryFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $config = config('Nadi');
        if (($config->driver ?? 'log') !== 'opentelemetry') {
            return $request;
        }

        try {
            $carrier = [];
            foreach ($request->headers() as $name => $header) {
                $carrier[strtolower($name)] = $header->getValueLine();
            }

            $context = TraceContextPropagator::getInstance()->extract($carrier);
            $spanName = $request->getMethod().' '.$request->getUri()->getPath();

            $tracer = \OpenTelemetry\API\Globals::tracerProvider()->getTracer('nadi-codeigniter');
            $span = $tracer->spanBuilder($spanName)
                ->setSpanKind(SpanKind::KIND_SERVER)
                ->setParent($context)
                ->startSpan();

            $scope = $span->activate();

            // Store span and scope for after() cleanup
            $request->setHeader('X-Nadi-Span', serialize([$span, $scope]));
        } catch (\Throwable $e) {
            // Silently ignore
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        try {
            $carrier = [];
            TraceContextPropagator::getInstance()->inject($carrier, null, Context::getCurrent());
            foreach ($carrier as $name => $value) {
                $response->setHeader($name, $value);
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }

        return $response;
    }
}
