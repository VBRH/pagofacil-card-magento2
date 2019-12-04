<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

class PrimitiveRequest extends Request
{
    /** @var array $primitiveBody */
    private $primitiveBody;

    /**
     * PrimitiveRequest constructor.
     * @param string $method
     * @param array $headers
     * @param array $body
     * @param string $version
     */
    public function __construct(string $method, array $headers = [], array $body, string $version = '1.1')
    {
        parent::__construct($method, $headers, 'nobody', $version);
        $this->primitiveBody = $body;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface|resource|string
     */
    public function getBody()
    {
        return urldecode(http_build_query($this->primitiveBody));
    }
}
