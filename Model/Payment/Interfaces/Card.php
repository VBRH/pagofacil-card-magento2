<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment\Interfaces;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;
use Psr\Http\Message\RequestInterface;

interface Card
{
    const CODE = 'pagofacil_card';

    /**
     * @param PagoFacilResponseInterface $response
     * @return Dto
     */
    public function getTransaction(PagoFacilResponseInterface $response): Dto;

    /**
     * @return array
     */
    public function getMonthlyInstallments(): array;

    public function createRequestTransaction(Order $order, Payment $payment): RequestInterface;
}
