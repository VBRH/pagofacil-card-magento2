<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Magento\Framework\App\ObjectManager;
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

    public function __construct(string $url)
    {
        $this->magentoClient = ObjectManager::getInstance()->get(HttpClientInterface::class);
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @return PagoFacilResponseInterface
     * @throws HttpException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        /** @var LoggerInterface $logger */
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);

        try {
            $this->magentoClient->post($this->url, $request->getBody());
        } catch (Exception $exception) {
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            throw new HttpException($exception->getMessage());
        }

        return new Response($this->magentoClient->getBody(), $this->magentoClient->getStatus());
    }
}
