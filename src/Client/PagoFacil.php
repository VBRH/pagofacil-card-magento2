<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Magento\Tests\NamingConvention\true\string;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Source\Client\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PagoFacil implements ClientInterface
{
    /** @var resource $curl */
    private $curl;
    /** @var string $url */
    private $url;

    public function __construct(string $url)
    {
        $this->curl = curl_init();
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws ClientException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $simpleResponse = null;

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            CURLOPT_POSTFIELDS => $request->getBody(),
        ]);

        $simpleResponse = curl_exec($this->curl);
        $error = curl_error($this->curl);
        curl_close($this->curl);

        if ($error) {
            throw new ClientException($error);
        }

        return new Response($simpleResponse, 200);
    }
}