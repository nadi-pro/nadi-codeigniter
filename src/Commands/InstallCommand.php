<?php

namespace Nadi\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Nadi\CodeIgniter\Shipper\Shipper;
use Nadi\Shipper\Exceptions\ShipperException;
use Nadi\Shipper\Exceptions\UnsupportedPlatformException;

class InstallCommand extends BaseCommand
{
    protected $group = 'Nadi';

    protected $name = 'nadi:install';

    protected $description = 'Install Nadi for CodeIgniter';

    public function run(array $params)
    {
        CLI::write('Installing Nadi for CodeIgniter...', 'green');

        $this->installShipper();

        CLI::write('Successfully installed Nadi', 'green');
    }

    private function installShipper(): void
    {
        CLI::write('Installing shipper binary...');

        try {
            $shipper = new Shipper;

            if ($shipper->isInstalled()) {
                $version = $shipper->getInstalledVersion() ?? 'unknown';
                CLI::write("Shipper already installed (version: {$version})");

                return;
            }

            $binaryPath = $shipper->install();
            $version = $shipper->getInstalledVersion() ?? 'unknown';
            CLI::write("Shipper installed (version: {$version})", 'green');
            CLI::write("Binary location: {$binaryPath}");
        } catch (UnsupportedPlatformException $e) {
            CLI::write('Shipper skipped: '.$e->getMessage(), 'yellow');
        } catch (ShipperException $e) {
            CLI::error('Failed: '.$e->getMessage());
        }
    }
}
