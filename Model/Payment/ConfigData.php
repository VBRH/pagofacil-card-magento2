<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

trait ConfigData
{
    /**
     * @param string $field
     * @param string $code
     * @return mixed
     */
    public function getConfigDataPagofacil(string $field, string $code)
    {
        $path = "payment/{$code}/$field";
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null);
    }
}