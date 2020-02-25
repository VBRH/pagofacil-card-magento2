<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Model\Payment;

use ArrayObject;
use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\{Customer, Address};
use PagoFacil\Payment\Exceptions\AmountException;
use PagoFacil\Payment\Exceptions\ClientException;
use PagoFacil\Payment\Exceptions\HttpException;
use PagoFacil\Payment\Exceptions\PaymentException;
use PagoFacil\Payment\Model\Payment\Abstracts\AbstractCard;
use PagoFacil\Payment\Model\Payment\Interfaces\Card;
use PagoFacil\Payment\Source\Client\Interfaces\ClientInterface;
use PagoFacil\Payment\Source\Client\PagoFacil as Client;
use PagoFacil\Payment\Source\Client\PrimitiveRequest;
use PagoFacil\Payment\Source\Transaction\Charge;
use PagoFacil\Payment\Source\User\Client as UserClient;
use PagoFacil\Payment\Source\Register;
use PagoFacil\Payment\Source\PagoFacilCardDataDto;
use Psr\Log\LoggerInterface;

class PagoFacilCard extends AbstractCard implements Card
{
    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return self
     * @throws AmountException
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount): self
    {
        /** @var Payment $payment */
        /** @var Order $order */
        /** @var UserClient $user */
        /** @var Customer $customer */
        /** @var LoggerInterface $logger */
        /** @var Address $billingAddress */
        /** @var Charge $charge */

        $charge = null;

        if ($amount <= 0) {
            throw new AmountException('Invalid amount');
        }

        $order = $payment->getOrder();
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
        $paymentData = new ArrayObject(Register::bringOut('card_data'));
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $customer = ObjectManager::getInstance()->get(CustomerFactory::class)->create()->load($order->getCustomerId());
        $billingAddress = $this->validateDefaultBillingAddress($customer->getDefaultBillingAddress());
        $user = Register::bringOut('user');
        $plan = 'NOR';
        $paymentData->offsetSet('plan', $plan);

        if(1 < intval($paymentData->offsetGet('monthly-installments'))) {
            $this->monthlyInstallmentsValidation(intval($paymentData->offsetGet('monthly-installments')));
            $plan = 'MSI';
            $paymentData->offsetSet('plan', $plan);
        }

        $cardDataDto = new PagoFacilCardDataDto($user, $order, $paymentData, $billingAddress, );

        try {
            $this->createTransactionInformation($cardDataDto);
        } catch (Exception $exception) {
            $logger->alert($exception->getTraceAsString());
        }

        try {
            if (is_null($payment->getParentTransactionId())) {
                $this->authorize($payment, $amount);
            }

            $order->setStatus(Order::STATE_PROCESSING);
            $payment->setIsTransactionClosed(true);

        } catch (ClientException|HttpException $exception) {
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $logger->error($exception->getExceptionCode());
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            throw $exception;
        } catch (PaymentException $exception) {
            $payment->setTransactionId($exception->getCharge()->getId());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $charge = $exception->getCharge();
            $logger->error($exception->getExceptionCode());
            $logger->error($exception->getMessage());
        } catch (Exception|AmountException $exception) {
            $logger->error($exception->getExceptionCode());
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            throw $exception;
        } finally {
            Register::removeInstance();
            if (!is_null($charge)) {
                throw $exception;
            }
        }

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|AbstractCard
     * @throws AmountException
     * @throws PaymentException
     * @throws HttpException
     * @throws ClientException
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        /** @var Payment $payment */
        /** @var Order $order */
        /** @var Charge $charge */
        /** @var LoggerInterface $logger */
        /** @var Client $httpClient */

        if ($amount <= 0) {
            throw new AmountException('Invalid amount auth');
        }
        $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $httpClient = Register::bringOut('client');
        $request = new PrimitiveRequest(
            ClientInterface::METHOD_TRANSACTION,
            Register::bringOut('transaccion')
        );

        $response = $httpClient->sendRequest($request);
        $response->validateAuthorized();
        $charge = $this->getTransaction($response);
        $logger->alert($charge->getMessage());
        $logger->alert($charge->getOrderId());

        $payment->setTransactionId($charge->getId());
        $payment->setParentTransactionId($charge->getId());
        $payment->setIsTransactionClosed(false);

        return $this;
    }
}
