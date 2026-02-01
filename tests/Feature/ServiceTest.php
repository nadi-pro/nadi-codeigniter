<?php

namespace Nadi\CodeIgniter\Tests\Feature;

use Nadi\CodeIgniter\NadiService;
use Nadi\CodeIgniter\Tests\TestCase;
use Nadi\CodeIgniter\Transporter;

class ServiceTest extends TestCase
{
    public function test_service_class_exists(): void
    {
        $this->assertTrue(class_exists(NadiService::class));
    }

    public function test_transporter_class_exists(): void
    {
        $this->assertTrue(class_exists(Transporter::class));
    }
}
