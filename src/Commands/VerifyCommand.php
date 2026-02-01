<?php

namespace Nadi\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Nadi\CodeIgniter\Nadi;

class VerifyCommand extends BaseCommand
{
    protected $group = 'Nadi';

    protected $name = 'nadi:verify';

    protected $description = 'Verify Nadi Configuration';

    public function run(array $params)
    {
        CLI::write('Verifying Nadi configuration...');

        $config = config('Nadi');
        CLI::write('Nadi monitoring: '.($config->enabled ? 'Enabled' : 'Disabled'));
        CLI::write("Driver: {$config->driver}");

        $result = Nadi::verify();

        if ($result) {
            CLI::write('Verification Status: OK', 'green');
        } else {
            CLI::error('Verification Status: Failed');
        }
    }
}
