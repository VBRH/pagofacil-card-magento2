<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Magento\Tests\NamingConvention\true\string;
use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface
{
    /** @var int $statusCode */
    private $statusCode;
    /** @var string $body */
    private $body;

    public function __construct(string $body, int $statusCode)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->getBody();
    }
}