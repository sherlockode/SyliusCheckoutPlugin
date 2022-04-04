<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Payum\Core\Payum;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class ChargeFactory
 */
class ChargeFactory
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * ChargeFactory constructor.
     *
     * @param Payum $payum
     */
    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return Charge
     */
    public function create(PaymentInterface $payment): Charge
    {
        $order = $payment->getOrder();
        $customer = $order->getCustomer();

        $details = $payment->getDetails();
        $charge = new Charge();
        if (isset($details['checkout']['token'])) {
            $charge->setToken($details['checkout']['token']);
        }
        if (isset($details['checkout']['instrument'])) {
            $charge->setInstrument($details['checkout']['instrument']);
        }
        $charge->setAmount($payment->getAmount());
        $charge->setCurrency($payment->getCurrencyCode());
        $charge->setReference($order->getNumber());
        $charge->setCapture(true);
        $charge->setSuccessUrl($this->getTokenUrl($payment, 'checkout_capture_success'));
        $charge->setFailureUrl($this->getTokenUrl($payment, 'checkout_capture_failure'));

        if ($customer) {
            $charge->setCustomerId($customer->getId());
            $charge->setCustomerName($customer->getFullName());
            $charge->setCustomerEmail($customer->getEmail());
        }

        return $charge;
    }

    /**
     * @param PaymentInterface $payment
     * @param string           $targetPath
     *
     * @return string
     */
    private function getTokenUrl(PaymentInterface $payment, string $targetPath): string
    {
        $token = $this->payum->getTokenFactory()->createToken('checkout', $payment, $targetPath);

        return $token->getTargetUrl();
    }
}
