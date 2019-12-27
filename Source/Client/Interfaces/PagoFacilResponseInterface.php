<?php

namespace PagoFacil\Payment\Source\Client\Interfaces;

use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Transaction\Charge;
use Psr\Http\Message\ResponseInterface;

interface PagoFacilResponseInterface extends ResponseInterface
{
    /**
     * @throws PaymentException
     */
    public function validateAuthorized(): void;

    /**
     * @return array
     */
    public function getBodyToArray(): array;

    /**
     * @return Charge
     */
    public function getTransaction(): Charge;
}
