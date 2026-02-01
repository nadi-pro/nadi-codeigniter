<?php

namespace Nadi\CodeIgniter\Shipper;

use Nadi\Shipper\BinaryManager;
use Nadi\Shipper\Exceptions\ShipperException;

class Shipper
{
    private BinaryManager $manager;

    public function __construct(?string $binaryDirectory = null)
    {
        $directory = $binaryDirectory ?? (defined('ROOTPATH') ? ROOTPATH.'vendor/bin' : getcwd().'/vendor/bin');
        $this->manager = new BinaryManager($directory);
    }

    public function install(?string $version = null): string
    {
        return $this->manager->install($version);
    }

    public function isInstalled(): bool
    {
        return $this->manager->isInstalled();
    }

    public function getBinaryPath(): string
    {
        return $this->manager->getBinaryPath();
    }

    public function getBinaryDirectory(): string
    {
        return $this->manager->getBinaryDirectory();
    }

    public function getInstalledVersion(): ?string
    {
        return $this->manager->getInstalledVersion();
    }

    public function needsUpdate(): bool
    {
        return $this->manager->needsUpdate();
    }

    public function update(): ?string
    {
        return $this->manager->update();
    }

    public function reInstall(?string $version = null): string
    {
        return $this->manager->reInstall($version);
    }

    public function getLatestVersion(): string
    {
        return $this->manager->getLatestVersion();
    }

    public function uninstall(): void
    {
        $this->manager->uninstall();
    }

    public function send(string $configPath): array
    {
        return $this->manager->execute(['--config='.$configPath, '--record']);
    }

    public function test(string $configPath): array
    {
        return $this->manager->execute(['--config='.$configPath, '--test']);
    }

    public function verify(string $configPath): array
    {
        return $this->manager->execute(['--config='.$configPath, '--verify']);
    }

    public function getManager(): BinaryManager
    {
        return $this->manager;
    }
}
