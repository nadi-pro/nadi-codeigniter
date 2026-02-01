<?php

namespace Nadi\CodeIgniter;

class Nadi
{
    private static ?Transporter $instance = null;

    public static function getInstance(): Transporter
    {
        if (static::$instance === null) {
            static::$instance = Transporter::make();
        }

        return static::$instance;
    }

    public static function setInstance(Transporter $transporter): void
    {
        static::$instance = $transporter;
    }

    public static function store(array $data): void
    {
        static::getInstance()->store($data);
    }

    public static function send(): void
    {
        static::getInstance()->send();
    }

    public static function test()
    {
        return static::getInstance()->test();
    }

    public static function verify()
    {
        return static::getInstance()->verify();
    }
}
