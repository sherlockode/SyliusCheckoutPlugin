<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Sherlockode\SyliusCheckoutPlugin\Payum\Action\StatusAction;

/**
 * Class CheckoutGatewayFactory
 */
class CheckoutGatewayFactory extends GatewayFactory
{
    /**
     * @param ArrayObject $config
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'checkout',
            'payum.factory_title' => 'Checkout',
            'payum.action.status' => new StatusAction(),
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new CheckoutApi(
                $config['public_key'],
                $config['secret_key'],
                (bool)$config['production'],
                $config['webhook_signature']
            );
        };

        $config['payum.paths'] = array_replace([
            'SherlockodeSyliusCheckoutPlugin' => __DIR__.'/../Resources/views',
        ], $config['payum.paths'] ?: []);
    }
}
