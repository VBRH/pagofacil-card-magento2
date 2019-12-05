<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client;

use Magento\Sales\Model\Order;
use PagoFacil\Payment\Source\User\Client;

class PrimitiveRequest extends Request
{
    /** @var array $primitiveBody */
    private $primitiveBody;

    /**
     * PrimitiveRequest constructor.
     * @param string $method
     * @param array $headers
     * @param array $body
     * @param string $version
     */
    public function __construct(string $method, array $body, array $headers = [], string $version = '1.1')
    {
        parent::__construct($method, $headers, 'nobody', $version);
        $this->primitiveBody = $body;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface|resource|string
     */
    public function getBody()
    {
        return urldecode(http_build_query($this->primitiveBody));
    }

    public static function transformData(Order $order, Order\Payment $payment, Client $user): array
    {
        return [
            'method' => ClientInterface::METHOD_TRANSACTION,
            'data' => [
                'idUsuario' => $user->getIdUser(),
                'idSucursal' => $user->getIdBranchOffice(),
                'idPedido' => $order->getId(),
                'monto' => $order->getGrandTotal(),
                'plan' => 'NOR',
                'mensualidad' => 0,
                'numeroTarjeta' => $payment->getCcNumberEnc(),
                'cvt' => $payment->getCcSecureVerify(),
                'mesExpiracion' => $payment->getCcExpMonth(),
                'anyoExpiracion' => $payment->getCcExpYear(),
                'nombre' => $order->getCustomerFirstname(),
                'apellidos' => $order->getCustomerLastname(),
                'cp' => $order->getCustomer()->getPrimaryBillingAddress()->getPostcode(),
                'email' => $order->getCustomerEmail(),
                'telefono' => $order->getCustomer()->getPrimaryBillingAddress()->getTelephone(),
                'celular' => $order->getCustomer()->getPrimaryBillingAddress()->getTelephone(),
                'calleyNumero' => $order->getBillingAddress()->getStreet(),
                'colonia' => $order->getBillingAddress()->getRegion(),
                'municipio' => '',
                'pais' => 'México',
                'estado' => $order->getBillingAddress()->getRegion(),
            ]
        ];
    }
}
