<?php

namespace Sherlockode\SyliusCheckoutPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
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
     * @var FactoryInterface
     */
    private $stateMachineFactory;

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
     * @param FactoryInterface         $stateMachineFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ClientFactory            $clientFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        FactoryInterface $stateMachineFactory,
        OrderRepositoryInterface $orderRepository,
        ClientFactory $clientFactory
    ) {
        $this->em = $em;
        $this->stateMachineFactory = $stateMachineFactory;
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
        $this->applyTransition($payment, $charge);

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
     *
     * @throws \SM\SMException
     */
    private function applyTransition(PaymentInterface $payment, Charge $charge): void
    {
        $transitions = [
            'Pending' => PaymentTransitions::TRANSITION_PROCESS,
            'Authorized' => PaymentTransitions::TRANSITION_AUTHORIZE,
            'Card Verified' => PaymentTransitions::TRANSITION_AUTHORIZE,
            'Voided' => PaymentTransitions::TRANSITION_CANCEL,
            'Partially Captured' => PaymentTransitions::TRANSITION_PROCESS,
            'Captured' => PaymentTransitions::TRANSITION_COMPLETE,
            'Partially Refunded' => PaymentTransitions::TRANSITION_REFUND,
            'Refunded' => PaymentTransitions::TRANSITION_REFUND,
            'Declined' => PaymentTransitions::TRANSITION_FAIL,
            'Canceled' => PaymentTransitions::TRANSITION_CANCEL,
            'Expired' => PaymentTransitions::TRANSITION_FAIL,
            'Paid' => PaymentTransitions::TRANSITION_COMPLETE,
        ];

        $transition = $transitions[$charge->getStatus()] ?? null;

        if ($transition) {
            $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);

            if ($stateMachine->can($transition)) {
                $stateMachine->apply($transition);
            }
        }
    }
}
