<?php

namespace Nadi\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Nadi\CodeIgniter\Shipper\Shipper;
use Nadi\Shipper\Exceptions\ShipperException;

class UpdateShipperCommand extends BaseCommand
{
    protected $group = 'Nadi';

    protected $name = 'nadi:update-shipper';

    protected $description = 'Update the Nadi shipper binary';

    public function run(array $params)
    {
        try {
            $shipper = new Shipper;

            if (CLI::getOption('force')) {
                CLI::write('Force re-installing shipper binary...');
                $version = $shipper->reInstall();
                CLI::write("Shipper installed (version: {$version})", 'green');

                return;
            }

            if (! $shipper->isInstalled()) {
                CLI::write('Shipper not installed. Run nadi:install first.', 'yellow');

                return;
            }

            if ($shipper->needsUpdate()) {
                $newVersion = $shipper->update();
                CLI::write("Shipper updated to: {$newVersion}", 'green');
            } else {
                CLI::write('Shipper is already up to date.');
            }
        } catch (ShipperException $e) {
            CLI::error('Failed: '.$e->getMessage());
        }
    }
}
