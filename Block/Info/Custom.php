<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Block\Info;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info;
use Psr\Log\LoggerInterface;

class Custom extends Info
{
    /**
     * Custom constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        /** @var LoggerInterface $logger */
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $logger->error('block');
        parent::__construct($context, $data);
        $this->_template = 'Pagofacil_Card::info/custom.phtml';
    }

    public function getOfflineInfo()
    {
        return $this->getMethod()->getInfoInstance()->getAdditionalInformation('');
    }

    public function getInstructions()
    {}
}