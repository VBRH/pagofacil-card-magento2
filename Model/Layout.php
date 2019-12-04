<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Layout implements ObserverInterface
{
    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * Layout constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info(
            ''
        );
        return $this;
    }
}
