<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source;

use ArrayObject;
use PagoFacil\Payment\Source\Interfaces\Dto;
use Magento\Customer\Model\{Customer, Address};
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PagoFacil\Payment\Source\User\Client;

class PaymentTranser implements Dto
{
    /** @var Order $order  */
    private $order;
    /** @var Client $user */
    private $user;
    /** @var ArrayObject $paymentData */
    private $paymentData;
    /** @var Address $billingAddress */
    private $billingAddress;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Client
     */
    public function getUser(): Client
    {
        return $this->user;
    }

    /**
     * @param Client $user
     */
    public function setUser(Client $user): void
    {
        $this->user = $user;
    }

    /**
     * @return ArrayObject
     */
    public function getPaymentData(): ArrayObject
    {
        return $this->paymentData;
    }

    /**
     * @param ArrayObject $paymentData
     */
    public function setPaymentData(ArrayObject $paymentData): void
    {
        $this->paymentData = $paymentData;
    }

    /**
     * @return Address
     */
    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }
}