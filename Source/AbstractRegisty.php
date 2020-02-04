<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source;

use ArrayObject;
use Exception;
use PagoFacil\Payment\Source\Interfaces\RegistryInterface;

class AbstractRegisty implements RegistryInterface
{
    protected static $instance;
    /** @var ArrayObject $data */
    protected $data;

    private function __construct()
    {
        $this->data = new ArrayObject();
    }

    static public function getInstance(): Register
    {
        if (!static::$instance instanceof Register) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get(string $key)
    {
        if (!$this->data->offsetExists($key)) {
            throw new Exception("Key {$key} not exists");
        }

        return $this->data->offsetGet($key);
    }

    /**
     * @param string $key
     * @param $value
     * @throws Exception
     */
    public function set(string $key, $value): void
    {
        if ($this->data->offsetExists($key)) {
            throw new Exception("The key {$key} are really exists");
        }

        $this->data->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @throws Exception
     */
    public function deleteKey(string $key): void
    {
        if (!$this->data->offsetExists($key)) {
            throw new Exception("Can't delete the key {$key} because not exists");
        }

        $this->data->offsetUnset($key);
    }

    public function deleteAll()
    {
        $this->data = null;
    }

    private function __wakeup()
    {
    }

    private function __clone()
    {
    }
}
