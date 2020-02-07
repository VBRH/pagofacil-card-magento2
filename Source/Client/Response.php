<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use InvalidArgumentException;
use Magento\Framework\App\ObjectManager;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface as HTTPInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\Transaction\Charge;
use Psr\Log\LoggerInterface;

class Response extends AbstractResponse
{
    /** @var int $statusCode */
    private $statusCode;
    /** @var string $body */
    private $body;
    /** @var array $arrayTransaction */
    private $arrayTransaction;

    /**
     * Response constructor.
     * @param string $body
     * @param int $statusCode
     * @throws InvalidArgumentException
     */
    public function __construct(string $body, int $statusCode, LoggerInterface $logger)
    {
        $this->validateStatusCodeRange($statusCode);
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->parseJsonToArray();
        $this->logger = $logger;

    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return string
     * @throws HttpException
     */
    public function getStatusCodeText(int $statusCode):string
    {
        if (!array_key_exists($statusCode, HTTPInterface::PHRASES)) {
            throw new HttpException('invalid_http_code');
        }

        return HTTPInterface::PHRASES[$statusCode];
    }

    /**
     * @return mixed|\Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $code
     * @throws InvalidArgumentException
     */
    protected function validateStatusCodeRange(int $code): void
    {
        if (100 > $code || 600 <= $code) {
            throw new \InvalidArgumentException(__('status code out of the range'));
        }
    }

    protected function parseJsonToArray(): void
    {
        $arrayResponse = json_decode($this->body, true);
        $this->arrayTransaction = $arrayResponse['WebServices_Transacciones'];
    }

    public function getBodyToArray(): array
    {
        return $this->arrayTransaction;
    }

    /**
     * @throws PaymentException
     * @throws \Exception
     */
    public function validateAuthorized(): void
    {
        if (!array_key_exists('autorizacion', $this->arrayTransaction['transaccion'])) {
            throw PaymentException::denied(
                $this->arrayTransaction['transaccion']['pf_message'],
                1,
                $this->getTransactionError(
                    $this->getTransaction()
                )
            );
        }
    }

    private function getTransactionError(Dto $charge): Charge
    {
        return Charge::setCode($charge, 1, 'deny_transaction');
    }

    /**
     * @return Charge
     * @throws ClientException
     */
    public function getTransaction(): Charge
    {
        $this->validateTransactionData();

        return new Charge(
            $this->getBodyToArray()['transaccion']['idTransaccion'],
            $this->getBodyToArray()['transaccion']['data']['idPedido'],
            $this->getBodyToArray()['transaccion']['pf_message']
        );
    }

    /**
     * @throws ClientException
     */
    protected function validateTransactionData():void
    {
        if(!in_array('idTransaccion', $this->getBodyToArray()['transaccion'])) {
            $this->logger->info($this->getBody());
            throw new ClientException("The transaction are failed, please connecting to local admin.");
        }
    }
}
