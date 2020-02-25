<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends AbstractException implements ClientExceptionInterface
{
    /** @var string $error_key */
    static public $error_key = 'error_admin_config';
    static public $error_transaction = 'error_transaction_pagofacil';

    /**
     * @param ClientException $object
     * @param string $errorCode
     * @return static
     */
    static public function setErrorCode(self $object, string $errorCode): self
    {
        $object->setExceptionCode($errorCode);

        return $object;
    }
}
