<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data;
use PagoFacil\Payment\Model\Payment\PagoFacilCard;
use Magento\Checkout\Model\Cart;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\App\ObjectManager;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Source\Logger as PagoFacilLogger;
use DateTime;
use Generator;

class PagoFacilConfigProvider implements ConfigProviderInterface
{
    use PagoFacilLogger;

    /** @var array $methodCodes */
    protected $methodCodes;
    /** @var AbstractMethod[] $methods */
    protected $methods;
    /** @var PagoFacilCard $payment */
    protected $payment;
    /** @var Cart $cart */
    protected $cart;
    /** @var UrlInterface */
    private $urlInterface;

    /**
     * PagoFacilConfigProvider constructor.
     * @param Data $data
     * @param PagoFacilCard $payment
     * @param Cart $cart
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(Data $data, PagoFacilCard $payment, Cart $cart)
    {
        $this->createLocalLogger(static::class);
        $this->payment = $payment;
        $this->cart = $cart;
        $this->methodCodes = [
            Card::CODE
        ];
        $this->methods = [];

        $this->methods[Card::CODE] = $data->getMethodInstance(Card::CODE);
        $this->urlInterface = ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $this->zendLogger->info('getConfig');
        if (!$this->methods[Card::CODE]->isAvailable()) {
            return [];
        }
        return [
            'payment' => [
                'ccform' => [
                    'months' => [
                        Card::CODE => $this->getMonths()
                    ],
                    'years' => [
                        Card::CODE => $this->getYears()
                    ],
                    'cvvImageUrl' => [
                        Card::CODE => $this->urlInterface->getUrl('pub/static/frontend/Magento/luma/es_MX/Magento_Checkout/') .'cvv.png'
                    ],
                    'ssStartYears' => [
                        Card::CODE => $this->getStartYear()
                    ],
                    'availableTypes' => [
                        Card::CODE => [
                            "AE" => "American Express",
                            "VI" => "Visa",
                            "MC" => "MasterCard"
                        ]
                    ],
                    'hasVerification' => [
                        Card::CODE => true
                    ],
                    'hasSsCardType' => [
                        Card::CODE => false
                    ],
                ]
            ]
        ];
    }

    public function getMonths(): array
    {
        return [
            "1" => "01 - Enero",
            "2" => "02 - Febrero",
            "3" => "03 - Marzo",
            "4" => "04 - Abril",
            "5" => "05 - Mayo",
            "6" => "06 - Junio",
            "7" => "07 - Julio",
            "8" => "08 - Agosto",
            "9" => "09 - Septiembre",
            "10"=> "10 - Octubre",
            "11"=> "11 - Noviembre",
            "12"=> "12 - Diciembre"
        ];
    }

    public function getYears(): array
    {
        $arrayYears = [];

        foreach ($this->yearGenerator() as $year) {
            $year = (string) $year;
            $arrayYears[$year] = $year;
        }

        return $arrayYears;
    }

    public function getStartYear(): array
    {
        $arrayYears = [];

        foreach ($this->startYearGenerator() as $year) {
            $year = (string) $year;
            $arrayYears[$year] = $year;
        }

        return $arrayYears;
    }

    protected function yearGenerator():Generator
    {
        $iterador = 0;
        $year = intval((new DateTime())->format('Y'));

        do{
            yield $year + $iterador;
            $iterador++;
        }while(10 >= $iterador);
    }

    protected function startYearGenerator():Generator
    {
        $year = intval((new DateTime())->format('Y'));

        for($iterador=5; $iterador>=0; $iterador--){
            yield ($year - $iterador);
        }
    }
}
