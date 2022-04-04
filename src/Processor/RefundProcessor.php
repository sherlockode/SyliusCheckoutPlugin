<?php

namespace Sherlockode\SyliusCheckoutPlugin\Processor;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\RefundFactory;
use Sherlockode\SyliusCheckoutPlugin\Payum\CheckoutApi;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

/**
 * Class RefundProcessor
 */
class RefundProcessor
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var RefundFactory
     */
    private $refundFactory;

    /**
     * RefundProcessor constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory, RefundFactory $refundFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->refundFactory = $refundFactory;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @throws \Exception
     */
    public function refund(PaymentInterface $payment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        if ($gatewayConfig->getGatewayName() !== 'checkout') {
            return;
        }

        $config = $gatewayConfig->getConfig();
        $api = new CheckoutApi($config['public_key'], $config['secret_key'], (bool)$config['production']);

        $details = $payment->getDetails();

        if (!isset($details['checkout']['id'])) {
            throw new UpdateHandlingException('Could not refund order');
        }

        $refund = $this->refundFactory->create($payment);
        $this->clientFactory->create($api)->refund($refund);

        if (!$refund->getId()) {
            throw new \Exception('Refund order failed');
        }
    }
}
