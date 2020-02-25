<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use PagoFacil\Payment\Model\Payment\Interfaces\ConfigInterface;
use PagoFacil\Payment\Source\Client\EndPoint;
use PagoFacil\Payment\Source\User\Client as UserClient;

trait ConfigData
{
    /** @var string $url */
    private $url;

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

    protected function getUrlEnviroment(string $field): void {
        if ($this->getConfigData('is_sandbox')) {
            $this->url = $this->getConfigDataPagofacil(
                'endpoint_sandbox',
                ConfigInterface::CODECONF
            );
        } else {
            $this->url = $this->getConfigDataPagofacil(
                'endpoint_production',
                ConfigInterface::CODECONF
            );
        }

        $this->endpoint = new EndPoint(
            $this->url,
            $this->getConfigData($field)
        );

        $this->user = new UserClient(
            $this->getConfigDataPagofacil(
                'display_user_id',
                ConfigInterface::CODECONF
            ),
            $this->getConfigDataPagofacil(
                'display_user_branch_office_id',
                ConfigInterface::CODECONF
            ),
            $this->getConfigDataPagofacil(
                'display_user_phase_id',
                ConfigInterface::CODECONF
            ),
            $this->endpoint
        );
    }
}
