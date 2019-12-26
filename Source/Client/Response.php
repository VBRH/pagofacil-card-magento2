<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use InvalidArgumentException;
use Magento\Framework\App\ObjectManager;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Client\ClientInterface as HTTPInterface;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\Transaction\Charge;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class Response implements PagoFacilResponseInterface
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
    public function __construct(string $body, int $statusCode)
    {
        $this->validateStatusCodeRange($statusCode);
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->parseJsonToArray();
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
            throw new \InvalidArgumentException('status code out of the range');
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

    public function getTransaction(): Charge
    {
        return new Charge(
            $this->getBodyToArray()['transaccion']['idTransaccion'],
            $this->getBodyToArray()['transaccion']['data']['idPedido'],
            $this->getBodyToArray()['transaccion']['pf_message']
        );
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
    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }
}
