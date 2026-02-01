<?php

namespace Nadi\CodeIgniter;

use CodeIgniter\Events\Events;
use Nadi\CodeIgniter\Handler\HandleExceptionEvent;
use Nadi\CodeIgniter\Handler\HandleQueryEvent;

class NadiService
{
    public static function register(): void
    {
        $config = config('Nadi');

        if (! ($config->enabled ?? true)) {
            return;
        }

        Nadi::setInstance(Transporter::make());

        static::registerExceptionHandler();
        static::registerQueryListener();
    }

    private static function registerExceptionHandler(): void
    {
        $previousHandler = set_exception_handler(null);
        restore_exception_handler();

        set_exception_handler(function (\Throwable $exception) use ($previousHandler) {
            try {
                $handler = new HandleExceptionEvent;
                $handler->handle($exception);
            } catch (\Throwable $e) {
                // Silently ignore monitoring errors
            }

            if ($previousHandler) {
                $previousHandler($exception);
            }
        });
    }

    private static function registerQueryListener(): void
    {
        Events::on('DBQuery', function ($query) {
            try {
                $handler = new HandleQueryEvent;
                $handler->handleQuery($query);
            } catch (\Throwable $e) {
                // Silently ignore monitoring errors
            }
        });
    }
}
