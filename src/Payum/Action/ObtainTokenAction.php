<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Checkout\CheckoutArgumentException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Generic;
use Payum\Core\Request\RenderTemplate;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Instrument;
use Sherlockode\SyliusCheckoutPlugin\Form\Type\ObtainTokenType;
use Sherlockode\SyliusCheckoutPlugin\Payum\Request\ObtainToken;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ObtainTokenAction
 */
class ObtainTokenAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * ObtainTokenAction constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param RequestStack         $requestStack
     * @param ClientFactory        $clientFactory
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        ClientFactory $clientFactory
    ) {
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        /** @var $request ObtainToken */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (!empty($details['checkout']['token']) || !empty($details['checkout']['instrument'])) {
            throw new LogicException('The token has already been set.');
        }

        $customer = $payment->getOrder()->getCustomer();
        $data = ['token' => null, 'rememberCard' => false];
        $sfRequest = $this->requestStack->getCurrentRequest();
        $form = $this->formFactory->create(ObtainTokenType::class, $data, [
            'action' => $request->getToken() ? $request->getToken()->getTargetUrl() : null,
            'instruments' => $this->getInstrumentChoices($payment),
            'allow_persist_instrument' => $customer && $customer->getUser(),
        ]);
        $form->handleRequest($sfRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $instrument = $form->has('instrument') ? $form->get('instrument')->getData() : null;
            if (is_object($instrument) && is_a($instrument, Instrument::class)) {
                $instrument = $instrument->getId();
            }
            $token = $form->get('token')->getData();

            if (!empty($token)) {
                $details['checkout'] = [
                    'token' => $token,
                    'instrument' => null,
                    'persist_instrument' => $form->has('rememberCard') ? $form->get('rememberCard')->getData() : false,
                ];
                $payment->setDetails($details);

                return;
            }

            if (!empty($instrument)) {
                $details['checkout'] = [
                    'token' => null,
                    'instrument' => $instrument,
                    'persist_instrument' => false,
                ];
                $payment->setDetails($details);

                return;
            }
        }

        $renderTemplate = new RenderTemplate('@SherlockodeSyliusCheckoutPlugin/Action/obtain_token.html.twig', [
            'publishable_key' => $this->api->getPublicKey(),
            'form' => $form->createView(),
            'debug' => !$this->api->isProduction(),
            'order' => $payment->getOrder(),
        ]);
        $this->gateway->execute($renderTemplate);

        throw new HttpResponse(
            $renderTemplate->getResult(),
            Response::HTTP_OK,
            [
                'Expires' => gmdate("D, d M Y H:i:s") . ' GMT',
                'Last-Modified' => gmdate("D, d M Y H:i:s") . ' GMT',
                'Cache-Control' => 'private, no-store, max-age=0, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof ObtainToken && $request->getModel() instanceof PaymentInterface;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     *
     * @throws CheckoutArgumentException
     */
    private function getInstrumentChoices(PaymentInterface $payment): array
    {
        if (!$payment->getOrder()->getCustomer()) {
            return [];
        }

        $customer = $this->clientFactory
            ->create($this->api)
            ->getCustomer($payment->getOrder()->getCustomer()->getEmail());

        if (!$customer) {
            return [];
        }

        return $customer->getInstruments();
    }
}
