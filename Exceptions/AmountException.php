<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Exceptions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Tests\NamingConvention\true\string;

class AmountException extends LocalizedException
{
    public function __construct(string $phrase, \Exception $cause = null, $code = 0)
    {
        parent::__construct(_($phrase), $cause, $code);
    }
}