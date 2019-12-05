<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use PagoFacil\Payment\Exceptions\AbstractException;

class PaymentException extends AbstractException
{
    static public function denied(string $message, int $code = 0): self
    {
        $denied = new static($message, $code);
        $denied->setExceptionCode('transaction_denied');

        return $denied;
    }

    static public function withoutFunds(string $message, int $code = 0): self
    {
        $funds = new static($message, $code);
        $funds->setExceptionCode('without_funds');

        return $funds;
    }
}