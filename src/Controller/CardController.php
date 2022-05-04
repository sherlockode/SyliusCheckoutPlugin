<?php

namespace Sherlockode\SyliusCheckoutPlugin\Controller;;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Factory\ClientFactory;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CardController
 */
class CardController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * CardController constructor.
     *
     * @param ClientFactory         $clientFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, ClientFactory $clientFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string $id
     *
     * @return Response
     */
    public function removeAction(string $id): Response
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if (!$user || !$user instanceof ShopUserInterface) {
            throw new AccessDeniedHttpException();
        }

        $client = $this->clientFactory->create();

        $shopCustomer = $user->getCustomer();
        $checkoutCustomer = $client->getCustomer($shopCustomer->getEmail());

        foreach ($checkoutCustomer->getInstruments() as $instrument) {
            if ($instrument->getId() === $id) {
                $client->removeInstrument($id);

                return new Response();
            }
        }

        throw new AccessDeniedHttpException();
    }
}
