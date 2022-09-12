<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Address;
use Sylius\Component\Core\Model\PaymentInterface;

class AddressFactory
{
    /**
     * @param PaymentInterface $payment
     *
     * @return Address|null
     */
    public function create(PaymentInterface $payment): ?Address
    {
        $billingAddress = $payment->getOrder()->getBillingAddress();

        if (!$billingAddress) {
            return null;
        }

        $address = new Address();
        $address->setStreet($billingAddress->getStreet());
        $address->setCity($billingAddress->getCity());
        $address->setZipCode($billingAddress->getPostcode());
        $address->setCountry($billingAddress->getCountryCode());

        return $address;
    }
}
