<?php

namespace Sherlockode\SyliusCheckoutPlugin\Twig\Extension;

use Sherlockode\SyliusCheckoutPlugin\Twig\SyliusCheckoutRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SyliusCheckoutExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('checkout_payment_locale', [SyliusCheckoutRuntime::class, 'checkoutPaymentLocale']),
        ];
    }
}
