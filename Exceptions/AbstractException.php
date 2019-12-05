<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use Exception;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractException extends LocalizedException
{
    /** @var string $exceptionCode */
    private $exceptionCode;

    public function __construct(string $phrase, $code = 0, Exception $cause = null)
    {
        parent::__construct(__($phrase), $cause, $code);
    }

    public function getExceptionCode(): string
    {
        return $this->exceptionCode;
    }

    protected function setExceptionCode(string $code): void
    {
        $this->exceptionCode = $code;
    }
}
