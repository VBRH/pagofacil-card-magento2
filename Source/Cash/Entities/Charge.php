<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Cash\Entities;

use DateTime;
use PagoFacil\Payment\Source\Interfaces\Dto;
use Ramsey\Uuid\Uuid;

class Charge implements Dto
{
    /** @var Uuid */
    private $id;
    /** @var string $reference */
    private $reference;
    /** @var string $customerOrder */
    private $customerOrder;
    /** @var float $amount */
    private $amount;
    /** @var float $storeFixedRate */
    private $storeFixedRate;
    /** @var string $storeSchedule */
    private $storeSchedule;
    /** @var string $storeImage */
    private $storeImage;
    /** @var Bank $bank */
    private $bank;
    /** @var DateTime $paydayLimit */
    private $paydayLimit;

    /**
     * Charge constructor.
     * @param Uuid $id
     * @param string $reference
     * @param string $customerOrder
     * @param float $amount
     * @param float $storeFixedRate
     * @param string $storeSchedule
     * @param string $storeImage
     * @param Bank $bank
     * @param DateTime $paydayLimit
     */
    public function __construct(
        Uuid $id,
        string $reference,
        string $customerOrder,
        float $amount,
        float $storeFixedRate,
        string $storeSchedule,
        string $storeImage,
        Bank $bank,
        DateTime $paydayLimit
    ) {
        $this->id = $id;
        $this->reference = $reference;
        $this->customerOrder = $customerOrder;
        $this->amount = $amount;
        $this->storeFixedRate = $storeFixedRate;
        $this->storeSchedule = $storeSchedule;
        $this->storeImage = $storeImage;
        $this->bank = $bank;
        $this->paydayLimit = $paydayLimit;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getCustomerOrder(): string
    {
        return $this->customerOrder;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getStoreFixedRate(): float
    {
        return $this->storeFixedRate;
    }

    /**
     * @return string
     */
    public function getStoreSchedule(): string
    {
        return $this->storeSchedule;
    }

    /**
     * @return string
     */
    public function getStoreImage(): string
    {
        return $this->storeImage;
    }

    /**
     * @return Bank
     */
    public function getBank(): Bank
    {
        return $this->bank;
    }

    /**
     * @return DateTime
     */
    public function getPaydayLimit(): DateTime
    {
        return $this->paydayLimit;
    }
}
