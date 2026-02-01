<?php

namespace Nadi\CodeIgniter\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Nadi\CodeIgniter\Handler\HandleHttpRequestEvent;

class NadiFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! defined('NADI_START_TIME')) {
            define('NADI_START_TIME', microtime(true));
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $config = config('Nadi');
        if (! ($config->enabled ?? true)) {
            return $response;
        }

        try {
            $startTime = defined('NADI_START_TIME') ? NADI_START_TIME : ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));

            if ($request instanceof IncomingRequest) {
                $handler = new HandleHttpRequestEvent;
                $handler->handle($request, $response, $startTime);
            }
        } catch (\Throwable $e) {
            // Silently ignore monitoring errors
        }

        return $response;
    }
}
