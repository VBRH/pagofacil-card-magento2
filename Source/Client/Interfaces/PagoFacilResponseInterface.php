<?php


namespace PagoFacil\Payment\Source\Client\Interfaces;


use PagoFacil\Payment\Exceptions\PaymentException;
use Psr\Http\Message\ResponseInterface;

interface PagoFacilResponseInterface extends ResponseInterface
{
    /**
     * @throws PaymentException
     */
    public function validateAuthorized(): void;

}