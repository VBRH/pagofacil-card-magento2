<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source;

use Zend\Log\Writer\Stream;

trait Logger
{
    /** @var \Zend\Log\Logger $zendLogger */
    private $zendLogger;
    /**
     * @param string $logName
     * @return void
     */
    protected function createLocalLogger(string $logName): void
    {
        $this->zendLogger = new \Zend\Log\Logger();
        $this->zendLogger->addWriter(new Stream(BP . "/var/log/{$logName}.log"));
    }

}