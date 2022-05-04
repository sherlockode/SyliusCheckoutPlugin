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
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\CustomerFactory;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\CreateCharge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\PersistCustomer;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class PersistCustomerAction
 */
class PersistCustomerAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * PersistCustomerAction constructor.
     *
     * @param ClientFactory   $clientFactory
     * @param CustomerFactory $customerFactory
     */
    public function __construct(ClientFactory $clientFactory, CustomerFactory $customerFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->customerFactory = $customerFactory;
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

        if (!$payment->getOrder()->getCustomer()) {
            return;
        }

        $client = $this->clientFactory->createFromApiObject($this->api);
        $customer = $client->getCustomer($payment->getOrder()->getCustomer()->getEmail());

        if (!$customer) {
            $customer = $this->customerFactory->create($payment);
            $client->createCustomer($customer);
        }

        $details = $payment->getDetails();
        $details['checkout']['customer_id'] = $customer->getId();
        $payment->setDetails($details);
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof PersistCustomer && $request->getModel() instanceof PaymentInterface;
    }
}
