<?php

namespace Nadi\CodeIgniter;

use Nadi\Sampling\Config;
use Nadi\Sampling\FixedRateSampling;
use Nadi\Sampling\SamplingManager;
use Nadi\Transporter\Contract;
use Nadi\Transporter\Service;

class Transporter
{
    protected string $driver;

    protected Contract $transporter;

    protected SamplingManager $samplingManager;

    protected Service $service;

    public function __construct()
    {
        $this->configureTransporter();
        $this->configureSampling();
        $this->service = new Service($this->transporter, $this->samplingManager);
    }

    private function configureTransporter(): void
    {
        $config = config('Nadi');
        $driverName = $config->driver ?? 'log';
        $this->driver = '\\Nadi\\Transporter\\'.ucfirst($driverName);

        if (! class_exists($this->driver)) {
            throw new \Exception("$this->driver did not exists");
        }

        if (! in_array(Contract::class, class_implements($this->driver))) {
            throw new \Exception("$this->driver did not implement the \Nadi\Transporter\Contract class.");
        }

        $connections = $config->connections ?? [];
        $this->transporter = (new $this->driver)
            ->configure($connections[$driverName] ?? []);
    }

    private function configureSampling(): void
    {
        $config = config('Nadi');
        $samplingConfig = $config->sampling ?? [];
        $samplingParams = $samplingConfig['config'] ?? [];

        $samplingConfigObj = new Config(
            samplingRate: $samplingParams['sampling_rate'] ?? 0.1,
            baseRate: $samplingParams['base_rate'] ?? 0.05,
            loadFactor: $samplingParams['load_factor'] ?? 1.0,
            intervalSeconds: $samplingParams['interval_seconds'] ?? 60
        );

        $strategies = $samplingConfig['strategies'] ?? [];
        $strategy = $samplingConfig['strategy'] ?? 'fixed_rate';

        $class = ! isset($strategies[$strategy])
            ? FixedRateSampling::class
            : $strategies[$strategy];

        if (! in_array(\Nadi\Sampling\Contract::class, class_implements($class))) {
            throw new \Exception("$class not implement \Nadi\Sampling\Contract", 500);
        }

        $this->samplingManager = new SamplingManager(new $class($samplingConfigObj));
    }

    public static function make(): self
    {
        return new self;
    }

    public function store(array $data)
    {
        return $this->service->handle($data);
    }

    public function send()
    {
        return $this->service->send();
    }

    public function test()
    {
        return $this->service->test();
    }

    public function verify()
    {
        return $this->service->verify();
    }

    public function __destruct()
    {
        return $this->service->send();
    }
}
