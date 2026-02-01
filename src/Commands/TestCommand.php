<?php

namespace Nadi\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Nadi\CodeIgniter\Nadi;

class TestCommand extends BaseCommand
{
    protected $group = 'Nadi';

    protected $name = 'nadi:test';

    protected $description = 'Test connectivity to Nadi API';

    public function run(array $params)
    {
        $config = config('Nadi');
        $driver = $config->driver ?? 'log';
        CLI::write("Testing Nadi connectivity using driver: {$driver}");

        $isActive = Nadi::test();

        if ($isActive) {
            CLI::write('Connectivity to Nadi is: Active', 'green');
        } else {
            CLI::error('Connectivity to Nadi is: Inactive');
        }
    }
}
