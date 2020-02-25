<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Transaction;

use PagoFacil\Payment\Source\Cash\Entities\Charge;
use PagoFacil\Payment\Source\Interfaces\Dto;

class OfflineInfo implements Dto
{
    /** @var int $error  */
    private $error;
    /** @var string */
    private $message;
    /** @var Charge */
    private $charge;

    /**
     * OfflineInfo constructor.
     * @param int $error
     * @param string $message
     * @param Charge $charge
     */
    public function __construct(int $error, string $message, Charge $charge)
    {
        $this->error = $error;
        $this->message = $message;
        $this->charge = $charge;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return Charge
     */
    public function getCharge(): Charge
    {
        return $this->charge;
    }
}
