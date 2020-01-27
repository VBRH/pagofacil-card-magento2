<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment\Abstracts;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Payment\Model\Method\Cc as CreditCard;
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\PagoFacilCardDataDto;
use PagoFacil\Payment\Source\Register;
use Psr\Log\LoggerInterface;

abstract class AbstractCard extends CreditCard
{
    protected $monthlyInstallments;

    /**
     * @param DataObject $data
     * @return $this|CreditCard
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(DataObject $data)
    {
        /** @var LoggerInterface $logger */
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        parent::assignData($data);

        try {
            Register::removeKey('municipality');
            Register::removeKey('suburb');
            Register::removeKey('card_data');
            Register::removeKey('monthly');
        } catch (Exception $exception) {
            $logger->alert($exception->getMessage());
        }

        if (empty($data->getData('additional_data')['billin-address-municipality'])) {
            throw new PaymentException('The field municipality is required.');
        }

        if (empty($data->getData('additional_data')['billin-address-municipality'])) {
            throw new PaymentException('The field suburb is required.');
        }

        Register::add('municipality', $data->getData('additional_data')['billin-address-municipality']);
        Register::add('suburb', $data->getData('additional_data')['billin-address-municipality']);
        Register::add('card_data', $data->getData('additional_data'));
        Register::add('monthly', $data->getData('additional_data')['monthly-installments']);

        return $this;
    }

    public function createTransactionInformation(Dto $cardData): void
    {
        /** @var PagoFacilCardDataDto $cardData */

        Register::add('transaccion', [
            'method' => ClientInterface::METHOD_TRANSACTION,
            'data' => [
                'idUsuario' => $cardData->getUser()->getIdUser(),
                'idSucursal' => $cardData->getUser()->getIdBranchOffice(),
                'idPedido' => $cardData->getOrder()->getRealOrderId(),
                'idServicio' => 3,
                'monto' => $cardData->getOrder()->getGrandTotal(),
                'plan' => $cardData->getPaymentData()->offsetGet('plan'),
                'mensualidad' => $cardData->getPaymentData()->offsetGet('monthly-installments'),
                'numeroTarjeta' => $cardData->getPaymentData()->offsetGet('cc_number'),
                'cvt' => $cardData->getPaymentData()->offsetGet('cc_cid'),
                'mesExpiracion' => $cardData->getPaymentData()->offsetGet('cc_exp_month'),
                'anyoExpiracion' => substr(
                    $cardData->getPaymentData()->offsetGet('cc_exp_year'), 2, 2
                ),
                'nombre' => $cardData->getOrder()->getCustomerName(),
                'apellidos' => $cardData->getOrder()->getCustomerLastname(),
                'cp' => $cardData->getBillingAddress()->getPostcode(),
                'email' => $cardData->getOrder()->getCustomerEmail(),
                'telefono' => $cardData->getBillingAddress()->getTelephone(),
                'celular' => $cardData->getBillingAddress()->getTelephone(),
                'calleyNumero' => $cardData->getBillingAddress()->getStreet()[0],
                'colonia' => Register::bringOut('suburb'),
                'municipio' => Register::bringOut('municipality'),
                'pais' => 'México',
                'estado' => $cardData->getBillingAddress()->getRegion()
            ]
        ]);
    }

    /**
     * @param PagoFacilResponseInterface $response
     * @return Dto
     */
    public function getTransaction(PagoFacilResponseInterface $response): Dto
    {
        return $response->getTransaction();
    }


    /**
     * @param int $month
     * @throws AmountException
     */
    public function monthlyInstallmentsValidation(int $month): void
    {
        if (!in_array($month, $this->getMonthlyInstallments())) {
            throw new AmountException('Invalid monthly installment');
        }
    }

    /**
     * @return array
     */
    public function getMonthlyInstallments(): array
    {
        /** @var array $months */
        $months = explode(',', $this->monthlyInstallments);

        if (!in_array("1", $months)) {
            array_push($months, "1");
        }
        asort($months);

        return $months;
    }
}