<?php

namespace PagoFacil\Payment\Source\Client\Interfaces;

use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Interfaces\Dto;
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
     * @return Dto
     * @throws ClientException
     */
    public function getTransaction(): Dto;
}
