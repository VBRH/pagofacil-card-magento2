<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Cc;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Source\Client\ClientInterface;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\PagoFacil;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\User\Client as UserClient;
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
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
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        if ($amount <= 0) {
            throw new AmountException('Invalid amount');
        }

        /** @var Payment $payment */
        /** @var Order $order */

        $payment->setAmount($amount);
        $order = $payment->getOrder();

        $payment->setAmount($amount);

        if ($this->isEmpty($payment->getLastTransId())) {
        }

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     */
    public function authorize(InfoInterface $payment, $amount): void
    {
        /** @var Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();
    }
}
