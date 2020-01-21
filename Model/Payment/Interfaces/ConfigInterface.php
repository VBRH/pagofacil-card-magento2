<?php


namespace PagoFacil\Payment\Model\Payment\Interfaces;


interface ConfigInterface
{
    const CODECONF = 'pagofacil_config';
    /**
     * @param string $field
     * @param string $code
     * @return mixed
     */
    public function getConfigDataPagofacil(string $field, string $code);
}