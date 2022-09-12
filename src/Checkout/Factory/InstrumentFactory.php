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
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param AddressFactory $addressFactory
     */
    public function __construct(AddressFactory $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

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
        $instrument->setAddress($this->addressFactory->create($payment));

        if (isset($details['checkout']['customer_id'])) {
            $instrument->setCustomerId($details['checkout']['customer_id']);
        }

        return $instrument;
    }
}
