<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Source\Client\ClientInterface as HTTPInterface;
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
        /** @var string $simpleResponse */
        $simpleResponse = null;
        /** @var string $error */
        $error = null;
        /** @var array $info */
        $info = null;

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
        $info = curl_getinfo($this->curl);
        $error = curl_error($this->curl);

        $statusCodeText = HTTPInterface::PHRASES[
            $info['http_code']
        ];

        curl_close($this->curl);

        if ($error) {
            throw new ClientException($error, $info['http_code']);
        }

        return new Response($simpleResponse, intval($info['http_code']));
    }
}
