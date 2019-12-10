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
        $request = new PrimitiveRequest(
            ClientInterface::POST,
            $this->requestData()
        );
    }

    /**
     * @test
     */
    public function ClientMockup(): void
    {
        $response = new Response(
            json_encode($this->responseData()),
            200
        );

        $request = new PrimitiveRequest(
            ClientInterface::POST,
            $this->requestData()
        );

        $client = $this->getMockBuilder(PagoFacil::class)
            ->setConstructorArgs(['https://demo.url'])
            ->setMethods(['sendRequest'])
            ->getMock();
        $client->expects($this->once())->with($request)->willReturn($response);
    }
}