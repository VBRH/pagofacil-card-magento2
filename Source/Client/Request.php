<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

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

    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    /**
     * @param string $string
     */
    protected function validateString(string $string): void
    {
        if (empty($string)) {
            throw new InvalidArgumentException('The string are not empty');
        }
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        // TODO: Implement getHeaders() method.
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        // TODO: Implement getHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    /**
     * @inheritDoc
     */
    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }
}
