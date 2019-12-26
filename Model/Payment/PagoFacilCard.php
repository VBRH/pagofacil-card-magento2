<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use ArrayAccess;
use ArrayObject;
use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Cc;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\{Customer, Address};
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Source\Client\ClientInterface;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\Transaction\Charge;
use PagoFacil\Payment\Source\User\Client as UserClient;
use PagoFacil\Payment\Source\Interfaces\Dto;
use PagoFacil\Payment\Source\Logger as PagoFacilLogger;
use PagoFacil\Payment\Source\Register;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class PagoFacilCard extends Cc implements Card
{
    use PagoFacilLogger;

    /** @var EndPoint $endpoint */
    private $endpoint;
    /** @var UserClient $user */
    private $user;
    /** @var Client $client  */
    private $client;
    private $monthlyInstallments;

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
     * @throws \Exception
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

        if ($this->getConfigData('is_sandbox')) {
            $url = $this->getConfigData('endpoint_sandbox');
        } else {
            $url = $this->getConfigData('endpoint_production');
        }

        if ((integer)$this->getConfigData('monthy_installment_enabled')) {
            $this->monthlyInstallments = $this->getConfigData('monthly_installments');
        }

        $this->endpoint = new EndPoint(
            $url,
            $this->getConfigData('uri_transaction')
        );

        $this->user = new UserClient(
            $this->getConfigData('display_user_id'),
            $this->getConfigData('display_user_branch_office_id'),
            $this->getConfigData('display_user_phase_id'),
            $this->endpoint
        );

        $this->zendLogger->err('constructor');

        try {
            Register::add('user', $this->user);
            Register::add(
                'client',
                new Client($this->user->getEndpoint()->getCompleteUrl())
            );
        } catch (Exception $exception) {
            $this->zendLogger->err($exception->getMessage());
            $this->zendLogger->err(json_encode(Register::getAll()));
        }
    }

    public function assignData(DataObject $data)
    {
        /** @var LoggerInterface $logger */
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        parent::assignData($data);

        Register::add('municipality', $data->getData('additional_data')['billin-address-municipality']);
        Register::add('suburb', $data->getData('additional_data')['billin-address-municipality']);
        Register::add('card_data', $data->getData('additional_data'));
        Register::add('monthly', $data->getData('additional_data')['monthly-installments']);

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return self
     * @throws AmountException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        if ($amount <= 0) {
            throw new AmountException('Invalid amount');
        }

        /** @var Payment $payment */
        /** @var Order $order */
        /** @var UserClient $user */
        /** @var Customer $customer */
        /** @var LoggerInterface $logger */
        /** @var Address $billingAddress */

        $order = $payment->getOrder();
        $order->setStatus(Order::STATE_PENDING_PAYMENT);

        $paymentData = new ArrayObject(Register::bringOut('card_data'));
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $customer = ObjectManager::getInstance()->get(CustomerFactory::class)->create()->load($order->getCustomerId());
        $billingAddress = $customer->getDefaultBillingAddress();
        $user = Register::bringOut('user');
        $plan = 'NOR';

        if(1 < intval($paymentData->offsetGet('monthly-installments'))) {
            $plan = 'MSI';
        }

        Register::add('transaccion', [
            'method' => ClientInterface::METHOD_TRANSACTION,
            'data' => [
                'idUsuario' => $user->getIdUser(),
                'idSucursal' => $user->getIdBranchOffice(),
                'idPedido' => $order->getRealOrderId(),
                'idServicio' => 3,
                'monto' => $order->getGrandTotal(),
                'plan' => $plan,
                'mensualidad' => $paymentData->offsetGet('monthly-installments'),
                'numeroTarjeta' => $paymentData->offsetGet('cc_number'),
                'cvt' => $paymentData->offsetGet('cc_cid'),
                'mesExpiracion' => $paymentData->offsetGet('cc_exp_month'),
                'anyoExpiracion' => $paymentData->offsetGet('cc_exp_year'),
                'nombre' => $order->getCustomerName(),
                'apellidos' => $order->getCustomerLastname(),
                'cp' => $billingAddress->getPostcode(),
                'email' => $order->getCustomerEmail(),
                'telefono' => $billingAddress->getTelephone(),
                'celular' => $billingAddress->getTelephone(),
                'calleyNumero' => $billingAddress->getStreet(),
                'colonia' => Register::bringOut('suburb'),
                'municipio' => Register::bringOut('municipality'),
                'pais' => 'México',
                'estado' => $billingAddress->getRegion()
            ]
        ]);

        $logger->info(json_encode(Register::bringOut('transaccion')));

        try {
            if (is_null($payment->getParentTransactionId())) {
                $this->authorize($payment, $amount);
            }
            $order->setStatus(Order::STATE_PROCESSING);
            $payment->setIsTransactionClosed(true);

        } catch (ClientException $exception) {
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
        } catch (PaymentException $exception) {
            $payment->setTransactionId($exception->getCharge()->getId());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
        }

        return $this;
    }

    public function authorize(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */
        /** @var Order $order */
        /** @var Charge $charge */
        /** @var LoggerInterface $logger */
        /** @var Client $httpClient */

        if ($amount <= 0) {
            throw new AmountException('Invalid amount auth');
        }
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $httpClient = Register::bringOut('client');

        $request = new PrimitiveRequest(
            ClientInterface::METHOD_TRANSACTION,
            Register::bringOut('transaccion')
        );

        $response = $httpClient->sendRequest($request);
        $charge = $this->getTransaction($response);
        throw new ClientException('Auth ha fallado y así');

        $payment->setTransactionId($charge->getId());
        $payment->setParentTransactionId($charge->getId());
        $payment->setIsTransactionClosed(false);

        $response->validateAuthorized();
        $payment->setTransactionId(65421);
        $payment->setParentTransactionId(65421);
        $payment->setIsTransactionClosed(false);

        return $this;
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
