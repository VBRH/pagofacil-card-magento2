<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

class EndPoint
{
    /** @var string $url */
    private $url;
    /** @var string $uri  */
    private $uri;

    public function __construct(string $url, string $uri)
    {
        $this->url = $url;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    public function getCompleteUrl(): string
    {
        return "{$this->url}{$this->uri}";
    }
}
