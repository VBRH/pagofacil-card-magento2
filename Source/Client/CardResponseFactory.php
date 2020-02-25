<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Client\Interfaces\ResponseFactory;
use Psr\Log\LoggerInterface;

class CardResponseFactory implements ResponseFactory
{

    /**
     * @param string $body
     * @param int $statusCode
     * @param LoggerInterface $logger
     * @return PagoFacilResponseInterface
     */
    public function createResponse(
        string $body, int $statusCode, LoggerInterface $logger
    ): PagoFacilResponseInterface
    {
        return new Response($body, $statusCode, $logger);
    }
}
