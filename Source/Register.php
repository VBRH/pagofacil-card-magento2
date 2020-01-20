<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source;

use Exception;

final class Register extends AbstractRegisty
{

    /**
     * @param string $key
     * @param $value
     * @throws Exception
     */
    static public function add(string $key, $value): void
    {
        static::getInstance()->set($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    static public function bringOut(string $key)
    {
        return static::getInstance()->get($key);
    }

    static public function getAll(): array
    {
        return static::getInstance()->data->getArrayCopy();
    }

    static public function removeInstance(): void
    {
        static::getInstance()->deleteAll();
        static::$instance = null;
    }

    /**
     * @param string $key
     * @throws Exception
     */
    static public function removeKey(string $key): void
    {
        static::getInstance()->deleteKey($key);
    }
}