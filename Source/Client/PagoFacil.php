<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Magento\Framework\HTTP\ClientInterface as HttpClientInterface;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class PagoFacil implements ClientInterface
{
    /** @var HttpClientInterface $magentoClient */
    private $magentoClient;
    /** @var string $url */
    private $url;
    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(
        string $url,
        HttpClientInterface $client,
        LoggerInterface $logger
    ) {
        $this->magentoClient = $client;
        $this->url = $url;
        $this->logger = $logger;
    }

    /**
     * @param RequestInterface $request
     * @return PagoFacilResponseInterface
     * @throws HttpException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = null;

        try {
            $this->magentoClient->post($this->url, $request->getBody());
             $response = new Response($this->magentoClient->getBody(), $this->magentoClient->getStatus());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
            throw new HttpException($exception->getMessage());
        }

        return $response;
    }
}
