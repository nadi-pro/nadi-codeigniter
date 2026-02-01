<?php

namespace Nadi\CodeIgniter\Support;

use Nadi\Support\OpenTelemetrySemanticConventions as CoreConventions;

class OpenTelemetrySemanticConventions extends CoreConventions
{
    public const CODEIGNITER_CONTROLLER = 'codeigniter.controller';

    public const CODEIGNITER_METHOD = 'codeigniter.method';

    public const CODEIGNITER_MODULE = 'codeigniter.module';

    public const DB_CONNECTION_NAME = 'db.connection.name';

    public const HTTP_CLIENT_DURATION = 'http.client.duration';

    public const HTTP_QUERY = 'http.query';

    public const HTTP_HEADERS = 'http.headers';

    public static function httpAttributesFromGlobals(): array
    {
        $attributes = [];

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $attributes[self::HTTP_METHOD] = $_SERVER['REQUEST_METHOD'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
            $attributes[self::HTTP_URL] = $scheme.'://'.$host.$_SERVER['REQUEST_URI'];
            $attributes[self::HTTP_SCHEME] = $scheme;
            $attributes[self::HTTP_HOST] = $host;
            $attributes[self::HTTP_TARGET] = $_SERVER['REQUEST_URI'];
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $attributes[self::HTTP_USER_AGENT] = $_SERVER['HTTP_USER_AGENT'];
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $attributes[self::HTTP_CLIENT_IP] = $_SERVER['REMOTE_ADDR'];
        }

        return $attributes;
    }

    public static function databaseAttributes(string $connectionName, string $query, float $duration): array
    {
        $attributes = [
            self::DB_SYSTEM => 'unknown',
            self::DB_STATEMENT => $query,
            self::DB_QUERY_DURATION => $duration,
        ];

        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|TRUNCATE)\s+/i', $query, $matches)) {
            $attributes[self::DB_OPERATION] = strtoupper($matches[1]);
        }

        if (preg_match('/(?:FROM|INTO|UPDATE|TABLE)\s+`?(\w+)`?/i', $query, $matches)) {
            $attributes[self::DB_SQL_TABLE] = $matches[1];
        }

        return $attributes;
    }

    public static function userAttributes(): array
    {
        try {
            if (function_exists('auth') && auth()->loggedIn()) {
                $user = auth()->user();

                return [
                    self::USER_ID => (string) ($user->id ?? ''),
                    self::USER_NAME => $user->username ?? '',
                    self::USER_EMAIL => $user->email ?? '',
                ];
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }

        return [];
    }

    public static function sessionAttributes(): array
    {
        if (session_status() === PHP_SESSION_ACTIVE && session_id()) {
            return [self::SESSION_ID => session_id()];
        }

        return [];
    }

    public static function exceptionAttributes(\Throwable $exception): array
    {
        return parent::exceptionAttributes($exception);
    }

    public static function performanceAttributes(float $startTime, ?int $memoryPeak = null): array
    {
        return parent::performanceAttributes($startTime, $memoryPeak);
    }
}
