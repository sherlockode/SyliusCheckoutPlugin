<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\Decline;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CreateChargeAction
 */
class DeclineAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param Generic $request
     *
     * @throws LogicException
     */
    public function execute($request): void
    {
        /** @var $request Decline */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        $details['checkout']['state'] = Charge::STATE_DECLINED;

        if ($this->api->isRetryDeclinedPayment()) {
            $history = $details['checkout']['history'] ?? [];
            $current = array_merge($details['checkout'], ['date' => date('Y-d-m H:i:s')]);
            unset($current['history']);

            $details['checkout'] = [
                'history' => array_merge($history, [$current])
            ];

            $payment->setState(PaymentInterface::STATE_NEW);
            $this->session->getFlashBag()->add('info', 'sylius.payment.failed');
        }

        $payment->setDetails($details);
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof Decline && $request->getModel() instanceof PaymentInterface;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return DeclineAction
     */
    public function setSession(RequestStack $requestStack): self
    {
        $this->session = $requestStack->getSession();

        return $this;
    }
}
