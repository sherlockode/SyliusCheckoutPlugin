<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ChargeFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\CreateCharge;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class CreateChargeAction
 */
class CreateChargeAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var ChargeFactory
     */
    private $chargeFactory;

    /**
     * CreateChargeAction constructor.
     *
     * @param ClientFactory $clientFactory
     * @param ChargeFactory $chargeFactory
     */
    public function __construct(ClientFactory $clientFactory, ChargeFactory $chargeFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->chargeFactory = $chargeFactory;
    }

    /**
     * @param Generic $request
     *
     * @throws LogicException
     */
    public function execute($request): void
    {
        /** @var $request CreateCharge */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        $client = $this->clientFactory->create($this->api);
        $charge = $this->chargeFactory->create($payment);

        try {
            $client->createCharge($charge);
        } catch (\Exception $e) {
            throw new HttpRedirect($charge->getFailureUrl());
        }

        $details['checkout'] = [
            'id' => $charge->getId(),
            'state' => $charge->getStatus(),
        ];
        $payment->setDetails($details);

        if ($charge->isSuccessful()) {
            throw new HttpRedirect($charge->getSuccessUrl());
        } elseif ($charge->hasRedirection()) {
            throw new HttpRedirect($charge->getRedirectionUrl());
        }

        throw new HttpRedirect($charge->getFailureUrl());
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof CreateCharge && $request->getModel() instanceof PaymentInterface;
    }
}
