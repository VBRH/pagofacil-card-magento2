<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use http\Exception\InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class Request implements RequestInterface
{
    /** @var string $method */
    private $method;
    /** @var array $headers */
    private $headers;
    /** @var string|resource|StreamInterface $body */
    private $body;
    /** @var string $version */
    private $version;
    /** @var string $protocol */
    private $protocol;

    /**
     * Request constructor.
     * @param string $method
     * @param array $headers
     * @param string|resource|StreamInterface $body
     * @param string $version
     */
    public function __construct(
        string $method,
        array $headers = [],
        $body,
        string $version = '1.1'
    ) {
        $this->method = strtoupper($method);
        $this->headers = $headers;
        $this->body = $body;
        $this->version = $version;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    protected function validateString(string $string): void
    {
        if (empty($string)) {
            throw new InvalidArgumentException('The string are not empty');
        }
    }
}
