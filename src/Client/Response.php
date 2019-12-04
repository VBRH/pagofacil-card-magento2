<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use InvalidArgumentException;
use Magento\Tests\NamingConvention\true\string;
use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface
{
    /** @var int $statusCode */
    private $statusCode;
    /** @var string $body */
    private $body;

    /**
     * Response constructor.
     * @param string $body
     * @param int $statusCode
     * @throws InvalidArgumentException
     */
    public function __construct(string $body, int $statusCode)
    {
        $this->validateStatusCodeRange($statusCode);
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed|\Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->getBody();
    }

    /**
     * @param int $code
     * @throws InvalidArgumentException
     */
    protected function validateStatusCodeRange(int $code): void
    {
        if (100 > $code || 600 <= $code) {
            throw new \InvalidArgumentException('status code out of the range');
        }
    }
}