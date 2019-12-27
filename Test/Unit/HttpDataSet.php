<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Test\Unit;

use PagoFacil\Payment\Source\Client\ClientInterface;
use Exception;

trait HttpDataSet
{
    /**
     * @return array
     * @throws Exception
     */
    public function requestData(): array
    {
        return [
            'method' => ClientInterface::METHOD_TRANSACTION,
            'data' => [
                'idUsuario' => 'f541b3f11f0f9b3fb33499684f22f6d711f2af58',
                'idSucursal' => 'e147ee31531d815e2308d6d6d39929ab599deb98',
                'idPedido' => bin2hex(random_bytes(5)),
                'monto' => 120.50,
                'plan' => 'NOR',
                'mensualidad' => 0,
                'numeroTarjeta' => '5105105105105100',
                'cvt' => '456',
                'mesExpiracion' => '05',
                'anyoExpiracion' => '20',
                'nombre' => 'John',
                'apellidos' => 'Doe',
                'cp' => 759641,
                'email' => 'john.doe@test.com',
                'telefono' => '5555555555',
                'celular' => '5555555555',
                'calleyNumero' => 'calle #93',
                'colonia' => 'Una colonia',
                'municipio' => 'Libre',
                'pais' => 'México',
                'estado' => 'CDMX',
            ]
        ];
    }

    /**
     * @return array
     */
    public function responseData(): array
    {
        return [
            'WebServices_Transacciones' =>
                [
                    'transaccion' =>
                        [
                            'autorizado' => '1',
                            'autorizacion' => '1',
                            'transaccion' => 'S-PFE2692S2720I1141743',
                            'texto' => 'Transaction has been successful!-staging',
                            'mode' => 'S',
                            'totalAttempts' => '1',
                            'idTransaccion' => '1141743',
                            'tipoTarjetaBancaria' => 'DEBIT',
                            'empresa' => 'PagoFacil Demo',
                            'TransIni' => '11:07:27 am 19/09/2018',
                            'TransFin' => '11:07:30 am 19/09/2018',
                            'param1' => '',
                            'param2' => '',
                            'param3' => '',
                            'param4' => '',
                            'param5' => '',
                            'TipoTC' => 'Master Card',
                            'data' =>
                                [
                                    'nombre' => 'Jon',
                                    'apellidos' => 'Snow',
                                    'numeroTarjeta' => '',
                                    'cvt' => '',
                                    'cp' => '48219',
                                    'mesExpiracion' => '',
                                    'anyoExpiracion' => '',
                                    'monto' => '1599',
                                    'idSucursal' => 'e147ee31531d815e2308d',
                                    'idUsuario' => 'f541b3f11f0f9b3fb33499',
                                    'idServicio' => '3',
                                    'email' => 'ohmlaud@gmail.com',
                                    'telefono' => '55751875',
                                    'celular' => '5530996234',
                                    'calleyNumero' => 'Valle de Don, 54',
                                    'colonia' => 'Del Valle',
                                    'municipio' => 'Tecamac',
                                    'estado' => 'Sonora',
                                    'pais' => 'México',
                                    'idPedido' => 'TEST_TX',
                                    'param1' => '',
                                    'param2' => '',
                                    'param3' => '',
                                    'param4' => '',
                                    'param5' => '',
                                    'httpUserAgent' => 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0',
                                    'ip' => '127.0.0.1',
                                    'transFechaHora' => '1537373247',
                                    'bin' => '5513 5',
                                ],
                            'dataVal' =>
                                [
                                    'idSucursal' => '2720',
                                    'cp' => '48219',
                                    'nombre' => 'Jon',
                                    'apellidos' => 'Snow',
                                    'numeroTarjeta' => '(16) **** **** ****2123',
                                    'cvt' => '(3) ***',
                                    'monto' => '1599',
                                    'mesExpiracion' => '(2) **',
                                    'anyoExpiracion' => '(2) **',
                                    'idUsuario' => '2691',
                                    'source' => '1',
                                    'idServicio' => '3',
                                    'recurrente' => '0',
                                    'plan' => 'NOR',
                                    'diferenciado' => '00',
                                    'mensualidades' => '00',
                                    'ip' => '201.140.96.58',
                                    'httpUserAgent' => 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0',
                                    'idPedido' => 'TEST_TX',
                                    'tipoTarjeta' => 'Master Card',
                                    'hashKeyCC' => '17b043d9be2648db6b298583',
                                    'idEmpresa' => '2692',
                                    'nombre_comercial' => 'PagoFacil Demo',
                                    'noProcess' => '',
                                    'noMail' => '',
                                    'notaMail' => '',
                                    'transFechaHora' => '1537373247',
                                    'settingsTransaction' =>
                                        [
                                            'noMontoMes' => '300',
                                            'noTransaccionesDia' => '2',
                                            'minTransaccionTc' => '5',
                                            'tiempoDevolucion' => '30',
                                            'sendPdfTransCliente' => '1',
                                            'AppService' => '1',
                                            'noTransErroneas' => '5',
                                            'minTransErroneas' => '10',
                                            'suspencionServicio' => '',
                                        ],
                                    'email' => 'pruebas@pagofacil.net',
                                    'telefono' => '55751875',
                                    'celular' => '5530996234',
                                    'calleyNumero' => 'Valle del Don, 45',
                                    'colonia' => 'Del Valle',
                                    'municipio' => 'Tecamac',
                                    'estado' => 'Sonora',
                                    'pais' => 'México',
                                    'idCaja' => '',
                                    'paisDetectedIP' => '201.140.96.58',
                                    'qa' => '1',
                                    'https' => 'off',
                                ],
                            'pf_message' => 'Transaccion exitosa',
                            'status' => 'success',
                        ],
                ],
        ];
    }

    /**
     * @return array
     */
    public function responseFailData(): array
    {
        return [
            'WebServices_Transacciones' =>
                [
                    'transaccion' =>
                        [
                            'autorizado' => '0',
                            'transaccion' => 'PFE8287S8591I863341',
                            'texto' => 'Transaction has been denied!',
                            'mode' => 'PRD',
                            'error' => 'Transacci%C3%B3n+inv%C3%A1lida',
                            'totalAttempts' => '1',
                            'idTransaccion' => '863341',
                            'tipoTarjetaBancaria' => 'DEBIT',
                            'empresa' => 'kevin medina',
                            'TransIni' => '12:32:17 pm 25/03/2019',
                            'TransFin' => '12:32:20 pm 25/03/2019',
                            'param1' => '',
                            'param2' => '',
                            'param3' => '',
                            'param4' => '',
                            'param5' => '',
                            'TipoTC' => 'Visa',
                            'data' =>
                                [
                                    'idSucursal' => '1775e5cbbb48ac261446e1add8d872e418d153dc',
                                    'idUsuario' => '838eb6af46d25064158149818d36a2dbb6771308',
                                    'idServicio' => '3',
                                    'nombre' => 'Mike',
                                    'apellidos' => 'Gonzalez',
                                    'numeroTarjeta' => '',
                                    'cvt' => '',
                                    'mesExpiracion' => '',
                                    'anyoExpiracion' => '',
                                    'monto' => '1.00',
                                    'email' => 'mike@pagofacil.net',
                                    'telefono' => '5513372748',
                                    'celular' => '5513374890',
                                    'calleyNumero' => 'anatole france 311',
                                    'colonia' => 'polanco',
                                    'municipio' => 'miguel hidalgo',
                                    'estado' => 'distrito federal',
                                    'pais' => 'mexico',
                                    'cp' => '11560',
                                    'idPedido' => 'TEST_TX',
                                    'param1' => '',
                                    'param2' => '',
                                    'param3' => '',
                                    'param4' => '',
                                    'param5' => '',
                                    'httpUserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                                    'ip' => '127.0.0.1',
                                    'EVT' => '4RD',
                                    'ENC_IP' => '55555555',
                                    'ENC_DIS' => '1231231',
                                    'transFechaHora' => '1553538737',
                                    'bin' => '492941',
                                ],
                            'dataVal' =>
                                [
                                    'idSucursal' => '8591',
                                    'cp' => '11560',
                                    'nombre' => 'Mike',
                                    'apellidos' => 'Gonzalez',
                                    'numeroTarjeta' => '(16) **** **** ****9384',
                                    'cvt' => '(3) ***',
                                    'monto' => '1.00',
                                    'mesExpiracion' => '(2) **',
                                    'anyoExpiracion' => '(2) **',
                                    'idUsuario' => '8389',
                                    'source' => '1',
                                    'idServicio' => '3',
                                    'recurrente' => '0',
                                    'plan' => 'NOR',
                                    'diferenciado' => '00',
                                    'mensualidades' => '00',
                                    'ip' => '201.140.96.58',
                                    'httpUserAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                                    'idPedido' => 'TEST_TX',
                                    'tipoTarjeta' => 'Visa',
                                    'hashKeyCC' => 'cb0b653e73a9f36cbf35df78983060bb760cdf0b',
                                    'idEmpresa' => '8287',
                                    'nombre_comercial' => 'kevin medina',
                                    'noProcess' => '',
                                    'noMail' => '',
                                    'notaMail' => '',
                                    'transFechaHora' => '1553538737',
                                    'settingsTransaction' =>
                                        [
                                            'noMontoMes' => '30000',
                                            'noTransaccionesDia' => '2',
                                            'minTransaccionTc' => '5',
                                            'tiempoDevolucion' => '60',
                                            'sendPdfTransCliente' => '0',
                                            'prodAltaDefault' => '7',
                                            'AppService' => '0',
                                            'noTransErroneas' => '5',
                                            'minTransErroneas' => '4',
                                            'suspencionServicio' => '',
                                        ],
                                    'email' => 'mike@pagofacil.net',
                                    'telefono' => '5513372748',
                                    'celular' => '5513374890',
                                    'calleyNumero' => 'anatole france 311',
                                    'colonia' => 'polanco',
                                    'municipio' => 'miguel hidalgo',
                                    'estado' => 'distrito federal',
                                    'pais' => 'mexico',
                                    'idCaja' => '',
                                    'paisDetectedIP' => '201.140.96.58',
                                    'qa' => '1',
                                    'https' => 'off',
                                ],
                            'pf_message' => 'Transaccion denegada',
                            'status' => 'success',
                        ],
                ],
        ];
    }
}