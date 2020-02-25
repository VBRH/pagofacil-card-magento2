<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface as HTTPInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\Transaction\Charge;

class Response extends AbstractResponse
{
    /** @var array $arrayTransaction */
    private $arrayTransaction;

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
        if(!array_key_exists('idTransaccion', $this->getBodyToArray()['transaccion'])) {
            $this->logger->info($this->getBody());
            throw ClientException::setErrorCode(
                new ClientException("The transaction are failed, please connecting to local admin."),
                ClientException::$error_key
            );
        }
    }
}
