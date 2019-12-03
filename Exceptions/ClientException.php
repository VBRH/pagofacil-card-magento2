<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use PagoFacil\Payment\Exceptions\AbstractException;
use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends AbstractException implements ClientExceptionInterface
{

}