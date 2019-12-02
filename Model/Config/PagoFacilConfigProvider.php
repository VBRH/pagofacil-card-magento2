<?php


namespace PagoFacil\Payment\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data;
use Magento\Checkout\Model\Cart;
use Magento\Payment\Model\Method\Logger;

class PagoFacilConfigProvider implements ConfigProviderInterface
{
    public function __construct(Data $paymentData, Cart $cart, Logger $logger)
    {
        $logger->debug(["hola" => "Como estas?"]);
    }

    public function getConfig()
    {
        return [];
    }
}