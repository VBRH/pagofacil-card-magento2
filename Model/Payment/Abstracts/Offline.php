<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment\Abstracts;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\HTTP\ClientInterface as HttpClientInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;
use PagoFacil\Payment\Block\Info\Custom;
use PagoFacil\Payment\Model\Payment\Interfaces\CashInterface;
use PagoFacil\Payment\Source\Client\CashResponseFactory;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Register;
use PagoFacil\Payment\Source\User\Client as UserClient;
use Psr\Log\LoggerInterface;

abstract class Offline extends AbstractMethod implements CashInterface
{
    /** @var LoggerInterface $logger */


    /** @var EndPoint $endpoint */
    protected $endpoint;
    /** @var UserClient $user */
    protected $user;
    /** @var Client $client  */
    protected $client;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData, ScopeConfigInterface $scopeConfig,
        Logger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->_isOffline = true;
        $this->_infoBlockType = Custom::class;
        $this->_code = static::CODE;
        $this->logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->init();

        try {
            Register::add('user', $this->user);
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
        }

        try {
            Register::add(
                'client',
                new Client(
                    $this->user->getEndpoint()->getCompleteUrl(),
                    ObjectManager::getInstance()->get(HttpClientInterface::class),
                    ObjectManager::getInstance()->get(LoggerInterface::class),
                    new CashResponseFactory()
                )
            );
        } catch (Exception $exception) {
            $this->logger->alert($exception->getMessage());
        }
    }

    public function buildRequest(Order $order): array {
        /** @var UserClient $user */
        $user = Register::bringOut('user');

        return [
            "branch_key" => $user->getIdBranchOffice(),
            "user_key" => $user->getIdUser(),
            "order_id" => $order->getId(),
            "product" => $this->getJsonItemsOrder($order),
            "amount" => $order->getGrandTotal(),
            "store_code" => "",
            "customer" => $order->getCustomer()->getName(),
            "email" => $order->getCustomerEmail()
        ];
    }

    /**
     * @param Order $order
     * @return string
     */
    protected function getJsonItemsOrder(Order $order): string
    {
        /** @var array $itemName */
        $itemName = [];

        foreach ($order->getAllItems() as $item) {
            $itemName[] = $item->getName();
        }

        return json_encode($itemName);
    }

    /**
     * If you need to add some functionality to the constructor, you can
     * use this function to avoid overwriting the construct
     */
    abstract protected function init(): void;

    /**
     * @param string $field
     */
    abstract protected function getUrlEnviroment(string $field): void;

    /**
     * @inheritDoc
     */
    abstract public function getConfigDataPagofacil(string $field, string $code);
}
