<?php


namespace PagoFacil\Payment\Source\Client\Interfaces;

use PagoFacil\Payment\Exceptions\PaymentException;
use Psr\Http\Message\ResponseInterface;
use PagoFacil\Payment\Source\Transaction\Charge;

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
