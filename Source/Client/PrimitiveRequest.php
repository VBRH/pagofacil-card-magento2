<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Psr\Http\Message\StreamInterface;

final class PrimitiveRequest extends Request
{
    /**
     * @var array
     */
    private $body;

    /**
     * PrimitiveRequest constructor.
     * @param string $method
     * @param array $body
     */
    public function __construct(string $method, array $body)
    {
        parent::__construct($method, [], '');
        $this->body = $body;
    }

    /**
     * @return StreamInterface|resource|string
     */
    public function getBody()
    {
        return urldecode(http_build_query($this->body));
    }

}
