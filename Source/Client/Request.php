<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Request extends AbstractRequest
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
        array $headers,
        $body,
        string $version = '1.1'
    ) {
        $this->method = strtoupper($method);
        $this->headers = $headers;
        $this->body = $body;
        $this->version = $version;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return StreamInterface|resource|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $string
     */
    protected function validateString(string $string): void
    {
        if (empty($string)) {
            throw new InvalidArgumentException(__('The string are not empty'));
        }
    }
}
