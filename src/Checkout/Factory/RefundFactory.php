<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Refund;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class ChargeFactory
 */
class RefundFactory
{
    /**
     * @param PaymentInterface $payment
     *
     * @return Refund
     */
    public function create(PaymentInterface $payment): Refund
    {
        $order = $payment->getOrder();
        $details = $payment->getDetails();

        $refund = new Refund();
        $refund->setPaymentId($details['checkout']['id']);
        $refund->setAmount($payment->getAmount());
        $refund->setReference($order->getNumber());

        return $refund;
    }
}
