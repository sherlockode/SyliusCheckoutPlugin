<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Sherlockode\SyliusCheckoutPlugin\Checkout\Client;
use Sherlockode\SyliusCheckoutPlugin\Payum\CheckoutApi;

/**
 * Class ClientFactory
 */
class ClientFactory
{
    /**
     * @param CheckoutApi $api
     *
     * @return Client
     */
    public function create(CheckoutApi $api): Client
    {
        return new Client($api->getPublicKey(), $api->getSecretKey(), $api->isProduction());
    }
}
