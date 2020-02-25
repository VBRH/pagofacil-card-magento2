<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment\Abstracts;

use Exception;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\ClientInterface as HttpClientInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\Cc as CreditCard;
use Magento\Payment\Model\Method\Logger;
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Model\Payment\ConfigData;
use PagoFacil\Payment\Source\Client\CardResponseFactory;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\Response;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\PagoFacilCardDataDto;
use PagoFacil\Payment\Source\Register;
use PagoFacil\Payment\Source\User\Client as UserClient;
use Psr\Log\LoggerInterface;

abstract class AbstractCard extends CreditCard
{
    use ConfigData;

    protected $monthlyInstallments;
    /** @var EndPoint $endpoint */
    private $endpoint;
    /** @var UserClient $user */
    private $user;
    /** @var Client $client  */
    private $client;

    /**
     * PagoFacilCard constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param ModuleListInterface $moduleList
     * @param TimezoneInterface $localeDate
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws Exception
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->createLocalLogger(static::CODE);
        $this->_isGateway = true;
        $this->_canCapture = true;
        $this->_canAuthorize = true;
        $this->_canCapturePartial = true;
        $this->_code = static::CODE;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );

        /** @var LoggerInterface $logger */
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->getUrlEnviroment('uri_transaction');

        if ((integer)$this->getConfigData('monthy_installment_enabled')) {
            $this->monthlyInstallments = $this->getConfigData('monthly_installments');
        }

        try {
            Register::add('user', $this->user);
        } catch (Exception $exception) {
            $logger->alert($exception->getMessage());
        }

        try {
            Register::add(
                'client',
                new Client(
                    $this->user->getEndpoint()->getCompleteUrl(),
                    ObjectManager::getInstance()->get(HttpClientInterface::class),
                    ObjectManager::getInstance()->get(LoggerInterface::class),
                    new CardResponseFactory()
                )
            );
        } catch (Exception $exception) {
            $logger->alert($exception->getMessage());
        }
    }
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
                'mensualidades' => $cardData->getPaymentData()->offsetGet('monthly-installments'),
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
     * @throws ClientException
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

    /**
     * @param $data
     * @return AbstractAddress
     * @throws ClientException
     */
    protected function validateDefaultBillingAddress($data): AbstractAddress
    {
        if (!$data instanceof AbstractAddress) {
            throw new ClientException("No have a default billing address, please complete the profile address configurations");
        }
        /** @var AbstractAddress $data */
        return $data;
    }
}
