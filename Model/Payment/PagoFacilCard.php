<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Cc;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Source\Client\ClientInterface;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\Interfaces\PagoFacilResponseInterface;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\User\Client as UserClient;
use PagoFacil\Payment\Source\Interfaces\Dto;
use Psr\Http\Message\RequestInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class PagoFacilCard extends Cc implements Card
{
    /** @var Logger $zendLogger */
    private $zendLogger;
    /** @var Stream */
    private $zendWriter;
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
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param ModuleListInterface $moduleList
     * @param TimezoneInterface $localeDate
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        ModuleListInterface $moduleList,
        TimezoneInterface $localeDate,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
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

        $this->createLocalLogger(static::CODE);

        $this->_isGateway = true;
        $this->_canOrder = true;
        $this->_canAuthorize = true;
        $this->_canCapture = true;
        $this->_canCapturePartial = true;
        $this->_canRefund = true;
        $this->_canRefundInvoicePartial = true;
        $this->_debugReplacePrivateDataKeys = [
            'number',
            'exp_month',
            'exp_year',
            'cvc'
        ];
        $this->_code = static::CODE;

        if ($this->getConfigData('is_sandbox')) {
            $url = $this->getConfigData('endpoint_sandbox');
        } else {
            $url = $this->getConfigData('endpoint_production');
        }
        $this->zendLogger->debug("is sandbox: {$this->getConfigData('is_sandbox')}");
        $this->zendLogger->debug("url: {$url}");

        $this->endpoint = new EndPoint(
            $url,
            $this->getConfigData('uri_transaction')
        );

        $this->zendLogger->debug("Url completa: {$this->endpoint->getCompleteUrl()}");

        $this->user = new UserClient(
            $this->getConfigData('display_user_id'),
            $this->getConfigData('display_user_branch_office_id'),
            $this->getConfigData('display_user_phase_id'),
            $this->endpoint
        );

        $this->client = new Client($this->user->getEndpoint()->getCompleteUrl());
    }

    private function createRequestTransaction(Order $order, Payment $payment): RequestInterface
    {
        return new PrimitiveRequest(
            ClientInterface::POST,
            PrimitiveRequest::transformData($order, $payment, $this->user),
            []
        );
    }

    /**
     * @param string $logName
     * @return void
     */
    protected function createLocalLogger(string $logName): void
    {
        $this->zendLogger = new Logger();
        $this->zendLogger->addWriter(new Stream(BP . "/var/log/{$logName}.log"));
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return self
     * @throws AmountException
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        if ($amount <= 0) {
            throw new AmountException('Invalid amount');
        }

        /** @var Payment $payment */
        /** @var Order $order */

        $payment->setAmount($amount);

        try {
            $this->authorize($payment, $amount);

            $payment->setIsTransactionClosed(true);
        } catch (ClientException $exception) {
            $this->zendLogger->err($exception->getMessage(), $exception->getTrace());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $payment->save();
        } catch (PaymentException $exception) {
            $this->zendLogger->err($exception->getMessage(), $exception->getTrace());
            $payment->setTransactionId($exception->getCharge()->getId());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $payment->save();
        }

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @throws ClientException
     * @throws PaymentException
     */
    public function authorize(InfoInterface $payment, $amount): void
    {
        /** @var Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();
        /** @var PagoFacilResponseInterface $response */
        $response = $this->client->sendRequest(
            $this->createRequestTransaction(
                $order,
                $payment
            )
        );

        $response->validateAuthorized();

        $charge = $response->getTransaction();

        $payment->setTransactionId($charge->getId());
        $payment->save();
    }

    /**
     * @param PagoFacilResponseInterface $response
     * @return Dto
     */
    public function getTransaction(PagoFacilResponseInterface $response): Dto
    {
        return $response->getTransaction();
    }
}
