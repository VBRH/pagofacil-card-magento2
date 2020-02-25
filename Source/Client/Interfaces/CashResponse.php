<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\Client\Interfaces;

use ArrayObject;
use DateTime;
use Exception;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Source\Cash\Entities\Bank;
use PagoFacil\Payment\Source\Client\AbstractResponse;
use PagoFacil\Payment\Source\Cash\Entities\Charge;
use PagoFacil\Payment\Source\Interfaces\Dto;
use Ramsey\Uuid\Uuid;

class CashResponse extends AbstractResponse
{
    /** @var ArrayObject $arrayBody */
    private $arrayBody;

    protected function parseJsonToArray(): void
    {
        $this->arrayBody = new ArrayObject(
            json_decode($this->body, true)
        );
    }

    public function getBodyToArray(): array
    {
        return $this->arrayBody->getArrayCopy();
    }

    /**
     * @return Charge
     * @throws ClientException
     * @throws Exception
     */
    public function getTransaction(): Dto
    {
        $this->validateTransactionData();

        $bank = new Bank(
            $this->arrayBody->offsetGet('charge')['bank'],
            $this->arrayBody->offsetGet('charge')['bank_account_number']
        );

        new Charge(
            Uuid::uuid4(),
            $this->arrayBody->offsetGet('charge')['referemce'],
            $this->arrayBody->offsetGet('charge')['customer_order'],
            $this->arrayBody->offsetGet('charge')['amount'],
            $this->arrayBody->offsetGet('charge')['store_fixed_rate'],
            $this->arrayBody->offsetGet('charge')['store_schedule'],
            $this->arrayBody->offsetGet('charge')['store_image'],
            $bank,
            new DateTime()
        );
    }

    /**
     * @throws ClientException
     */
    protected function validateTransactionData(): void
    {
        if ('1' === $this->arrayBody->offsetGet('error')) {
            throw ClientException::setErrorCode(
                new ClientException("Transaction are failed."),
                ClientException::$error_transaction
            );
        }
    }
}
