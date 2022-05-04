<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\InstrumentFactory;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\CreateCharge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\PersistCustomer;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\PersistInstrument;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class PersistInstrumentAction
 */
class PersistInstrumentAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var InstrumentFactory
     */
    private $instrumentFactory;

    /**
     * PersistInstrumentAction constructor.
     *
     * @param ClientFactory     $clientFactory
     * @param InstrumentFactory $instrumentFactory
     */
    public function __construct(ClientFactory $clientFactory, InstrumentFactory $instrumentFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->instrumentFactory = $instrumentFactory;
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

        $persistCustomer = new PersistCustomer($request->getToken());
        $persistCustomer->setModel($payment);
        $this->gateway->execute($persistCustomer);

        $instrument = $this->instrumentFactory->create($payment);
        $this->clientFactory->createFromApiObject($this->api)->createInstrument($instrument);

        if ($instrument->getId()) {
            $details['checkout']['instrument'] = $instrument->getId();
            $details['checkout']['token'] = null;
            $payment->setDetails($details);
        }
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof PersistInstrument && $request->getModel() instanceof PaymentInterface;
    }
}
