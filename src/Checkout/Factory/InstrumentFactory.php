<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Instrument;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class InstrumentFactory
 */
class InstrumentFactory
{
    /**
     * @param PaymentInterface $payment
     *
     * @return Instrument
     */
    public function create(PaymentInterface $payment): Instrument
    {
        $details = $payment->getDetails();

        $instrument = new Instrument();
        $instrument->setToken($details['checkout']['token']);

        if (isset($details['checkout']['customer_id'])) {
            $instrument->setCustomerId($details['checkout']['customer_id']);
        }

        return $instrument;
    }
}
