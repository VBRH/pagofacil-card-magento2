<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Exception;

abstract class AbstractException extends LocalizedException
{
    public function __construct(string $phrase, $code = 0, Exception $cause = null)
    {
        parent::__construct(_($phrase), $cause, $code);
    }
}