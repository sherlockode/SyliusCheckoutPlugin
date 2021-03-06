<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\CreateCharge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\ObtainToken;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\PersistInstrument;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\Capture;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CaptureAction
 */
class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var UrlGeneratorInterface
     */
    public $urlGenerator;

    /**
     * CaptureAction constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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

        if (PaymentInterface::STATE_FAILED === $payment->getState()) {
            throw new HttpRedirect($this->urlGenerator->generate('sylius_shop_cart_summary'));
        }

        if (empty($details['checkout']['token']) && empty($details['checkout']['instrument'])) {
            $obtainToken = new ObtainToken($request->getToken());
            $obtainToken->setModel($payment);
            $this->gateway->execute($obtainToken);
        }

        $details = $payment->getDetails();
        $persist = $details['checkout']['persist_instrument'] ?? false;

        if ($persist) {
            $persistInstrument = new PersistInstrument($request->getToken());
            $persistInstrument->setModel($payment);
            $this->gateway->execute($persistInstrument);
        }

        $this->gateway->execute(new CreateCharge($request->getToken()));
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }
}
