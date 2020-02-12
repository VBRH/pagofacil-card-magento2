<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source;

use ArrayObject;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Sales\Model\Order;
use PagoFacil\Payment\Source\User\Client;
use PagoFacil\Payment\Source\Interfaces\Dto;

class PagoFacilCardDataDto implements Dto
{
    /** @var Client $user */
    private $user;
    /** @var Order $order */
    private $order;
    /** @var string $pan */
    /** @var ArrayObject $paymentData */
    private $paymentData;
    /** @var Address $billingAddress */
    private $billingAddress;
    /** @var string $suburb */
    private $suburb;
    /** @var string $municipality */
    private $municipality;

    /**
     * PagoFacilCardDataDto constructor.
     * @param Client $user
     * @param Order $order
     * @param ArrayObject $paymentData
     * @param Address $billingAddress
     * @param string $suburb
     * @param string $municipality
     */
    public function __construct(
        Client $user,
        Order $order,
        ArrayObject $paymentData,
        AbstractAddress $billingAddress
    ) {
        $this->user = $user;
        $this->order = $order;
        $this->paymentData = $paymentData;
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Client
     */
    public function getUser(): Client
    {
        return $this->user;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return ArrayObject
     */
    public function getPaymentData(): ArrayObject
    {
        return $this->paymentData;
    }

    /**
     * @return Address
     */
    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    /**
     * @return string
     */
    public function getSuburb(): string
    {
        return $this->suburb;
    }

    /**
     * @return string
     */
    public function getMunicipality(): string
    {
        return $this->municipality;
    }

}