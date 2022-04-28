<?php

namespace Sherlockode\SyliusCheckoutPlugin\Controller;

use Payum\Core\Payum;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\Confirm;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\Decline;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CheckoutController
 */
class CheckoutController
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * CheckoutController constructor.
     *
     * @param Payum $payum
     */
    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function successAction(Request $request)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);
        $identity = $token->getDetails();
        /** @var PaymentInterface $payment */
        $payment = $this->payum->getStorage($identity->getClass())->find($identity);

        $gateway = $this->payum->getGateway('checkout');
        $gateway->execute(new Confirm($payment));

        $this->payum->getHttpRequestVerifier()->invalidate($token);
        $afterPayToken = $this->payum->getTokenFactory()->createToken(
            'checkout',
            $payment,
            'sylius_shop_order_after_pay'
        );

        return new RedirectResponse($afterPayToken->getTargetUrl());
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function failureAction(Request $request)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);
        $identity = $token->getDetails();
        /** @var PaymentInterface $payment */
        $payment = $this->payum->getStorage($identity->getClass())->find($identity);

        $gateway = $this->payum->getGateway('checkout');
        $gateway->execute(new Decline($payment));

        $this->payum->getHttpRequestVerifier()->invalidate($token);
        $tokenFactory = $this->payum->getTokenFactory();

        $details = $payment->getDetails();

        if (isset($details['checkout']['state']) && Charge::STATE_DECLINED === $details['checkout']['state']) {
            $nextToken = $tokenFactory->createToken('checkout', $payment, 'sylius_shop_order_after_pay');
        } else {
            $nextToken = $tokenFactory->createCaptureToken('checkout', $payment, 'sylius_shop_order_after_pay');
        }

        return new RedirectResponse($nextToken->getTargetUrl());
    }
}
