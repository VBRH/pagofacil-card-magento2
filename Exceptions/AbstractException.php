<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use Exception;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractException extends LocalizedException
{
    /** @var string $exceptionCode */
    private $exceptionCode;

    /**
     * AbstractException constructor.
     * @param string $phrase
     * @param int $code
     * @param Exception|null $cause
     */
    public function __construct(string $phrase, $code = 0, Exception $cause = null)
    {
        parent::__construct(__($phrase), $cause, $code);
    }

    /**
     * @return string
     */
    public function getExceptionCode(): string
    {
        return $this->exceptionCode;
    }

    /**
     * @param string $code
     */
    protected function setExceptionCode(string $code): void
    {
        $this->exceptionCode = $code;
    }
}
