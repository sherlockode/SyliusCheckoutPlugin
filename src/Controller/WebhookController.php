<?php

namespace Sherlockode\SyliusCheckoutPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
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
     * WebhookController constructor.
     *
     * @param EntityManagerInterface   $em
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(EntityManagerInterface $em, OrderRepositoryInterface $orderRepository)
    {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
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

        $this->setPaymentStatus($payment, $payload);
        $this->setOrderStatus($payment->getOrder(), $payload);

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
     * @param array            $payload
     */
    private function setPaymentStatus(PaymentInterface $payment, array $payload): void
    {
        if (!isset($payload['type'])) {
            return;
        }

        $states = [
            'card_verification_declined' => PaymentInterface::STATE_FAILED,
            'card_verified' => PaymentInterface::STATE_AUTHORIZED,
            'payment_pending' => PaymentInterface::STATE_PROCESSING,
            'payment_approved' => PaymentInterface::STATE_AUTHORIZED,
            'payment_paid' => PaymentInterface::STATE_COMPLETED,
            'payment_declined' => PaymentInterface::STATE_FAILED,
            'payment_canceled' => PaymentInterface::STATE_CANCELLED,
            'payment_expired' => PaymentInterface::STATE_FAILED,
            'payment_capture_pending' => PaymentInterface::STATE_PROCESSING,
            'payment_capture_declined' => PaymentInterface::STATE_FAILED,
            'payment_captured' => PaymentInterface::STATE_COMPLETED,
            'payment_voided' => PaymentInterface::STATE_CANCELLED,
        ];

        if (isset($states[$payload['type']])) {
            $payment->setState($states[$payload['type']]);
        }
    }

    /**
     * @param OrderInterface $order
     * @param array          $payload
     */
    private function setOrderStatus(OrderInterface $order, array $payload): void
    {
        if (!isset($payload['type'])) {
            return;
        }

        $states = [
            'card_verification_declined' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'card_verified' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_pending' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_approved' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_paid' => OrderPaymentStates::STATE_PAID,
            'payment_declined' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_canceled' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_expired' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_capture_pending' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_capture_declined' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
            'payment_captured' => OrderPaymentStates::STATE_PAID,
            'payment_voided' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
        ];

        if (isset($states[$payload['type']])) {
            $order->setPaymentState($states[$payload['type']]);
        }
    }
}
