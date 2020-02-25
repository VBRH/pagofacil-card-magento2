<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use PagoFacil\Payment\Source\Client\Interfaces\CashResponse;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Client\Interfaces\ResponseFactory;
use Psr\Log\LoggerInterface;

class CashResponseFactory implements ResponseFactory
{

    /**
     * @param string $body
     * @param int $statusCode
     * @param LoggerInterface $logger
     * @return PagoFacilResponseInterface
     */
    public function createResponse(
        string $body,
        int $statusCode,
        LoggerInterface $logger
    ): PagoFacilResponseInterface {
        return new CashResponse($body, $statusCode, $logger);
    }
}
