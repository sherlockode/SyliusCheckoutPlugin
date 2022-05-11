<?php

namespace Sherlockode\SyliusCheckoutPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WebhookController
 */
class WebhookController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * WebhookController constructor.
     *
     * @param EntityManagerInterface   $em
     * @param OrderRepositoryInterface $orderRepository
     * @param ClientFactory            $clientFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        OrderRepositoryInterface $orderRepository,
        ClientFactory $clientFactory
    ) {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);
        $payment = $this->getPayment($payload);

        $this->verifyRequest($payment, $request);

        $charge = $this->getPaymentState($payment);
        $this->setPaymentStatus($payment, $charge);
        $this->setOrderStatus($payment->getOrder(), $charge);

        $this->em->flush();

        return new Response();
    }

    /**
     * @param PaymentInterface $payment
     * @param Request          $request
     */
    private function verifyRequest(PaymentInterface $payment, Request $request): void
    {
        $gatewayConfig = $payment->getMethod()->getGatewayConfig();
        $config = $gatewayConfig->getConfig();

        if (
            'checkout' !== $gatewayConfig->getGatewayName() ||
            !isset($config['webhook_signature']) ||
            !$request->headers->has('authorization') ||
            $config['webhook_signature'] !== $request->headers->get('authorization')
        ) {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return Charge
     */
    private function getPaymentState(PaymentInterface $payment): Charge
    {
        $details = $payment->getDetails();

        if (!isset($details['checkout']['id'])) {
            throw new NotFoundHttpException();
        }

        $client = $this->clientFactory->create();
        $charge = $client->getCharge($details['checkout']['id']);

        if (!$charge) {
            throw new NotFoundHttpException();
        }

        return $charge;
    }

    /**
     * @param array $payload
     *
     * @return PaymentInterface|null
     */
    private function getPayment(array $payload): ?PaymentInterface
    {
        $paymentId = $payload['data']['id'] ?? null;
        $orderNumber = $payload['data']['reference'] ?? null;

        if (!$paymentId || !$orderNumber) {
            throw new NotFoundHttpException();
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        if (!$order) {
            throw new NotFoundHttpException();
        }

        foreach ($order->getPayments() as $payment) {
            $details = $payment->getDetails();

            if (!isset($details['checkout']['id'])) {
                continue;
            }

            if ($paymentId === $details['checkout']['id']) {
                return $payment;
            }
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param PaymentInterface $payment
     * @param Charge           $charge
     */
    private function setPaymentStatus(PaymentInterface $payment, Charge $charge): void
    {
        $states = [
            'Pending' => PaymentInterface::STATE_PROCESSING,
            'Authorized' => PaymentInterface::STATE_AUTHORIZED,
            'Card Verified' => PaymentInterface::STATE_AUTHORIZED,
            'Voided' => PaymentInterface::STATE_CANCELLED,
            'Partially Captured' => PaymentInterface::STATE_PROCESSING,
            'Captured' => PaymentInterface::STATE_COMPLETED,
            'Partially Refunded' => PaymentInterface::STATE_REFUNDED,
            'Refunded' => PaymentInterface::STATE_REFUNDED,
            'Declined' => PaymentInterface::STATE_FAILED,
            'Canceled' => PaymentInterface::STATE_CANCELLED,
            'Expired' => PaymentInterface::STATE_FAILED,
            'Paid' => PaymentInterface::STATE_COMPLETED,
        ];

        if (isset($states[$charge->getStatus()])) {
            $payment->setState($states[$charge->getStatus()]);
        }
    }

    /**
     * @param OrderInterface $order
     * @param Charge         $charge
     */
    private function setOrderStatus(OrderInterface $order, Charge $charge): void
    {
        $states = [
            'Pending' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Authorized' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Card Verified' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Voided' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Partially Captured' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Captured' => OrderPaymentStates::STATE_PAID,
            'Partially Refunded' => OrderPaymentStates::STATE_REFUNDED,
            'Refunded' => OrderPaymentStates::STATE_REFUNDED,
            'Declined' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Canceled' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Expired' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'Paid' => OrderPaymentStates::STATE_PAID,
        ];

        if (isset($states[$charge->getStatus()])) {
            $order->setPaymentState($states[$charge->getStatus()]);
        }
    }
}
