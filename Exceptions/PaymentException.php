<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use PagoFacil\Payment\Exceptions\AbstractException;
use PagoFacil\Payment\Source\Transaction\Charge;
use PagoFacil\Payment\Source\Interfaces\Dto;

class PaymentException extends AbstractException
{
    /** @var Charge $charge  */
    private $charge;

    /**
     * @param string $message
     * @param int $code
     * @param Dto $charge
     * @return static
     */
    public static function denied(string $message, int $code, Dto $charge): self
    {
        $denied = new static($message, $code);
        $denied->setExceptionCode('transaction_denied');
        $denied->charge = $charge;

        return $denied;
    }

    public static function withoutFunds(string $message, int $code = 0): self
    {
        $funds = new static($message, $code);
        $funds->setExceptionCode('without_funds');

        return $funds;
    }

    public function getCharge(): Charge
    {
        return $this->charge;
    }
}
