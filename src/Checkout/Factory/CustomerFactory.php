<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Customer;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class CustomerFactory
 */
class CustomerFactory
{
    /**
     * @param PaymentInterface $payment
     *
     * @return Customer
     */
    public function create(PaymentInterface $payment): Customer
    {
        $checkoutCustomer = new Customer();
        $order = $payment->getOrder();
        $customer = $order->getCustomer();

        if ($customer) {
            $checkoutCustomer->setEmail($customer->getEmail());
            $checkoutCustomer->setFullName($customer->getFullName());
        }

        return $checkoutCustomer;
    }
}
