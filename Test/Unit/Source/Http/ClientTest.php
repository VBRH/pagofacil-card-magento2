<?php

declare(strict_types=1);

namespace Test\Unit\Source\Http;

use PagoFacil\Payment\Source\Client\ClientInterface;
use PagoFacil\Payment\Source\Client\PagoFacil;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\Client\Response;
use PagoFacil\Payment\Source\Transaction\Charge;
use PagoFacil\Payment\Test\Unit\HttpDataSet;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    use HttpDataSet;

    /**
     * @test
     */
    public function response(): void
    {
        $response = new Response(
            json_encode($this->responseData()),
            200
        );

        $this->assertTrue(is_numeric($response->getStatusCode()));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(array_key_exists(
            'autorizacion',
            $response->getBodyToArray()['transaccion']
        ));
        $this->assertInstanceOf(Charge::class, $response->getTransaction());

    }

    /**
     * @test
     */
    public function request(): void
    {
        $match = null;
        $request = new PrimitiveRequest(
            ClientInterface::POST,
            $this->requestData()
        );

        preg_match('/=transaccion/', $request->getBody(), $match);
        $this->assertGreaterThanOrEqual(1, count($match));

        preg_match(
            '/\[monto\]\=([0-9]*[.]?[0-9]+)/', $request->getBody(), $match
        );
        $this->assertGreaterThanOrEqual(1, count($match));
    }
}