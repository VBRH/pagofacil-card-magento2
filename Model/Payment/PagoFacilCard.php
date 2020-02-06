<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use ArrayObject;
use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\{Customer, Address};
use Magento\Framework\HTTP\ClientInterface as HttpClientInterface;
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Model\Payment\Abstracts\AbstractCard;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Model\Payment\Interfaces\ConfigInterface;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\Transaction\Charge;
use PagoFacil\Payment\Source\User\Client as UserClient;
use PagoFacil\Payment\Source\Logger as PagoFacilLogger;
use PagoFacil\Payment\Source\Register;
use PagoFacil\Payment\Source\PagoFacilCardDataDto;
use Psr\Log\LoggerInterface;

class PagoFacilCard extends AbstractCard implements Card
{
    use PagoFacilLogger;
    use ConfigData;

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

        if ($this->getConfigData('is_sandbox')) {
            $url = $this->getConfigDataPagofacil('endpoint_sandbox', ConfigInterface::CODECONF);
        } else {
            $url = $this->getConfigDataPagofacil('endpoint_production', ConfigInterface::CODECONF);
        }

        if ((integer)$this->getConfigData('monthy_installment_enabled')) {
            $this->monthlyInstallments = $this->getConfigData('monthly_installments');
        }

        $this->endpoint = new EndPoint(
            $url,
            $this->getConfigData('uri_transaction')
        );

        $this->user = new UserClient(
            $this->getConfigDataPagofacil('display_user_id', ConfigInterface::CODECONF),
            $this->getConfigDataPagofacil('display_user_branch_office_id', ConfigInterface::CODECONF),
            $this->getConfigDataPagofacil('display_user_phase_id', ConfigInterface::CODECONF),
            $this->endpoint
        );

        try {
            Register::add('user', $this->user);
        } catch (Exception $exception) {
            $this->zendLogger->alert($exception->getMessage());
        }

        try {
            Register::add(
                'client',
                new Client(
                    $this->user->getEndpoint()->getCompleteUrl(),
                    ObjectManager::getInstance()->get(HttpClientInterface::class),
                    ObjectManager::getInstance()->get(LoggerInterface::class)
                )
            );
        } catch (Exception $exception) {
            $this->zendLogger->alert($exception->getMessage());
        }
    }


    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return self
     * @throws AmountException
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        /** @var Payment $payment */
        /** @var Order $order */
        /** @var UserClient $user */
        /** @var Customer $customer */
        /** @var LoggerInterface $logger */
        /** @var Address $billingAddress */
        /** @var Charge $charge */

        $charge = null;

        if ($amount <= 0) {
            throw new AmountException('Invalid amount');
        }

        $order = $payment->getOrder();
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
        $paymentData = new ArrayObject(Register::bringOut('card_data'));
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $customer = ObjectManager::getInstance()->get(CustomerFactory::class)->create()->load($order->getCustomerId());
        $billingAddress = $this->validateDefaultBillingAddress($customer->getDefaultBillingAddress());
        $user = Register::bringOut('user');
        $plan = 'NOR';
        $paymentData->offsetSet('plan', $plan);

        if(1 < intval($paymentData->offsetGet('monthly-installments'))) {
            $this->monthlyInstallmentsValidation(intval($paymentData->offsetGet('monthly-installments')));
            $plan = 'MSI';
            $paymentData->offsetSet('plan', $plan);
        }

        $cardDataDto = new PagoFacilCardDataDto($user, $order, $paymentData, $billingAddress, );

        try {
            $this->createTransactionInformation($cardDataDto);
        } catch (Exception $exception) {
            $logger->alert($exception->getTraceAsString());
        }

        try {
            if (is_null($payment->getParentTransactionId())) {
                $this->authorize($payment, $amount);
            }

            $order->setStatus(Order::STATE_PROCESSING);
            $payment->setIsTransactionClosed(true);

        } catch (ClientException|HttpException $exception) {
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            throw $exception;
        } catch (PaymentException $exception) {
            $payment->setTransactionId($exception->getCharge()->getId());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $charge = $exception->getCharge();
            $logger->error($exception->getMessage());
        } catch (Exception $exception) {
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            throw $exception;
        } finally {
            Register::removeInstance();
            if (!is_null($charge)) {
                throw $exception;
            }
        }

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|AbstractCard
     * @throws AmountException
     * @throws PaymentException
     * @throws HttpException
     */
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
        $response->validateAuthorized();
        $charge = $this->getTransaction($response);
        $logger->alert($charge->getMessage());
        $logger->alert($charge->getOrderId());

        $payment->setTransactionId($charge->getId());
        $payment->setParentTransactionId($charge->getId());
        $payment->setIsTransactionClosed(false);

        return $this;
    }
}
