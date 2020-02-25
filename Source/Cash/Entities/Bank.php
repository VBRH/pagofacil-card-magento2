<?php

declare(strict_types=1);
namespace PagoFacil\Payment\Source\Cash\Entities;

use PagoFacil\Payment\Source\Interfaces\ValueObject;

class Bank implements ValueObject
{
    /** @var string $name  */
    private $name;
    private $account;

    /**
     * Bank constructor.
     * @param string $name
     * @param $account
     */
    public function __construct(string $name, $account)
    {
        $this->name = $name;
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }
}
