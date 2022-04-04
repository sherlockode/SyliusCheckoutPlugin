<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum\Action;

use Payum\Core\Exception\UnsupportedApiException;
use Sherlockode\SyliusCheckoutPlugin\Payum\CheckoutApi;

/**
 * Trait ApiAwareTrait
 */
trait ApiAwareTrait
{
    /**
     * @var CheckoutApi
     */
    private $api;

    /**
     * @param CheckoutApi $api
     */
    public function setApi($api): void
    {
        if (!$api instanceof CheckoutApi) {
            throw new UnsupportedApiException(sprintf(
                'Not supported. Expects an instance of %s',
                CheckoutApi::class
            ));
        }

        $this->api = $api;
    }
}
