<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\Confirm;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class ConfirmAction
 */
class ConfirmAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * ConfirmAction constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (!isset($details['checkout']['id'])) {
            throw new LogicException('Invalid Checkout ID.');
        }

        $charge = $this->clientFactory->create($this->api)->getCharge($details['checkout']['id']);

        $details['checkout']['state'] = $charge ? $charge->getStatus() : Charge::STATE_DECLINED;
        $payment->setDetails($details);
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof Confirm && $request->getModel() instanceof PaymentInterface;
    }
}
