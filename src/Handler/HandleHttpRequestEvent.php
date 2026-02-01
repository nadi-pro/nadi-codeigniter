<?php

namespace Nadi\CodeIgniter\Handler;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use Nadi\CodeIgniter\Data\Entry;
use Nadi\CodeIgniter\Support\OpenTelemetrySemanticConventions;
use Nadi\Data\Type;

class HandleHttpRequestEvent extends Base
{
    public function handle(IncomingRequest $request, ResponseInterface $response, float $startTime): void
    {
        $config = config('Nadi');
        $statusCode = $response->getStatusCode();
        $ignoredCodes = $config->http['ignored_status_codes'] ?? [];

        if (in_array($statusCode, $ignoredCodes)) {
            return;
        }

        $uri = (string) current_url();
        $method = $request->getMethod();
        $title = "$uri returned HTTP Status Code $statusCode";

        $otelAttributes = OpenTelemetrySemanticConventions::httpAttributesFromGlobals();
        $userAttributes = OpenTelemetrySemanticConventions::userAttributes();
        $sessionAttributes = OpenTelemetrySemanticConventions::sessionAttributes();
        $performanceAttributes = OpenTelemetrySemanticConventions::performanceAttributes($startTime, memory_get_peak_usage(true));
        $otelData = array_merge($otelAttributes, $userAttributes, $sessionAttributes, $performanceAttributes);

        $entryData = [
            'title' => $title,
            'description' => "$uri for $method request returned HTTP Status Code $statusCode",
            'uri' => $uri,
            'method' => $method,
            'headers' => $this->headers($request),
            'payload' => $this->payload($request),
            'response_status' => $statusCode,
            'response' => $this->formatResponse($response),
            'duration' => floor((microtime(true) - $startTime) * 1000),
            'memory' => round(memory_get_peak_usage(true) / 1024 / 1025, 1),
            'otel' => $otelData,
        ];

        $this->store(Entry::make(
            Type::HTTP,
            $entryData
        )->setHashFamily(
            $this->hash($method.$statusCode.$uri.date('Y-m-d H'))
        )->tags([
            $method,
            $statusCode,
            'http.method:'.$method,
            'http.status_code:'.$statusCode,
        ])->toArray());
    }

    protected function headers(IncomingRequest $request): array
    {
        $config = config('Nadi');
        $hidden = $config->http['hidden_request_headers'] ?? [];
        $headers = [];

        foreach ($request->headers() as $name => $header) {
            $value = $header->getValueLine();
            if (in_array(strtolower($name), $hidden)) {
                $value = '********';
            }
            $headers[$name] = $value;
        }

        return $headers;
    }

    protected function payload(IncomingRequest $request): array
    {
        $config = config('Nadi');
        $hidden = $config->http['hidden_parameters'] ?? [];
        $data = $request->getPost() ?: $request->getJSON(true) ?: [];

        if (is_array($data)) {
            foreach ($hidden as $param) {
                if (isset($data[$param])) {
                    $data[$param] = '********';
                }
            }
        }

        return is_array($data) ? $data : [];
    }

    protected function formatResponse(ResponseInterface $response): string
    {
        $body = $response->getBody() ?? '';

        if (strlen($body) > 64000) {
            return 'Purged By Nadi';
        }

        $decoded = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }

        return 'HTML Response';
    }
}
