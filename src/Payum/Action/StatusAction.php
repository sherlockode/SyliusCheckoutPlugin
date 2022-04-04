<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sylius\Component\Core\Model\PaymentInterface;

class StatusAction implements ActionInterface
{
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        $details = $payment->getDetails();
        $statuses = [Charge::STATE_AUTHORIZED, Charge::STATE_PAID, Charge::STATE_CAPTURED];

        if (
            isset($details['checkout']['state']) &&
            in_array($details['checkout']['state'], $statuses)
        ) {
            $request->markCaptured();

            return;
        }

        $request->markFailed();
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof PaymentInterface;
    }
}
