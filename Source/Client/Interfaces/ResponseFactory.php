<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client\Interfaces;

use Psr\Log\LoggerInterface;

interface ResponseFactory
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
    ): PagoFacilResponseInterface;
}
