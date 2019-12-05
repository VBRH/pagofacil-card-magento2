<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Transaction;

use PagoFacil\Payment\Source\Interfaces\Dto;

class Charge implements Dto
{
    /** @var string $id */
    private $id;
    /** @var string $orderId */
    private $orderId;
    /** @var string $message  */
    private $message;
    /** @var string $statusCode  */
    private $statusCode;
    /** @var int */
    private $code;

    /**
     * Charge constructor.
     * @param string $id
     * @param string $orderId
     * @param string $message
     */
    public function __construct(string $id, string $orderId, string $message)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param Charge $charge
     * @param int $code
     * @param string $statusCode
     * @return static
     */
    public static function setCode(self $charge, int $code, string $statusCode): self
    {
        $charge->code = $code;
        $charge->statusCode = $statusCode;

        return $charge;
    }
}
