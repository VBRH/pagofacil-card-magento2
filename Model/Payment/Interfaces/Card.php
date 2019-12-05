<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment\Interfaces;

use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;

interface Card
{
    const CODE = 'pagofacil_card';

    /**
     * @param PagoFacilResponseInterface $response
     * @return Dto
     */
    public function getTransaction(PagoFacilResponseInterface $response): Dto;
}
