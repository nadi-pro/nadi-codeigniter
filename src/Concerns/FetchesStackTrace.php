<?php

namespace Nadi\CodeIgniter\Concerns;

trait FetchesStackTrace
{
    protected function getCallerFromStackTrace(int $forgetLines = 0): ?array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = array_slice($trace, $forgetLines);

        foreach ($trace as $frame) {
            if (! isset($frame['file'])) {
                continue;
            }

            $ignored = $this->ignoredPaths();
            $isIgnored = false;

            foreach ($ignored as $path) {
                if (str_contains($frame['file'], $path)) {
                    $isIgnored = true;

                    break;
                }
            }

            if (! $isIgnored) {
                return $frame;
            }
        }

        return null;
    }

    protected function ignoredPaths(): array
    {
        $basePath = defined('ROOTPATH') ? ROOTPATH : getcwd().'/';

        return [
            $basePath.'vendor',
        ];
    }
}
