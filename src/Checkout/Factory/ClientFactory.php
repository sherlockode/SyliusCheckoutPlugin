<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Client;
use Sherlockode\SyliusCheckoutPlugin\Payum\CheckoutApi;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;

/**
 * Class ClientFactory
 */
class ClientFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ClientFactory constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CheckoutApi $api
     *
     * @return Client
     */
    public function createFromApiObject(CheckoutApi $api): Client
    {
        return new Client($api->getPublicKey(), $api->getSecretKey(), $api->isProduction());
    }

    /**
     * @param GatewayConfigInterface $gatewayConfig
     *
     * @return Client
     */
    public function createFromGatewayConfig(GatewayConfigInterface $gatewayConfig): Client
    {
        $config = $gatewayConfig->getConfig();

        return $this->createFromApiObject(new CheckoutApi(
            $config['public_key'],
            $config['secret_key'],
            (bool)$config['production'],
            $config['webhook_signature'],
            (bool)$config['retry_declined_payment']
        ));
    }

    /**
     * @return Client
     *
     * @throws \Exception
     */
    public function create(): Client
    {
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $this->em->getRepository(GatewayConfig::class)->findOneBy([
            'factoryName' => 'sylius.checkout',
        ]);

        if (!$gatewayConfig) {
            throw new \Exception('Unknown gateway "sylius.checkout"');
        }

        return $this->createFromGatewayConfig($gatewayConfig);
    }
}
