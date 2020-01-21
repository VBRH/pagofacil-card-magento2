<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Interfaces;

interface RegistryInterface
{
    public function get(string $key);
    public function set(string $key, $value): void;
    public function deleteKey(string $key): void;
    public function deleteAll();
}