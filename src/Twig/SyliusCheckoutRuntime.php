<?php

namespace Sherlockode\SyliusCheckoutPlugin\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class SyliusCheckoutRuntime implements RuntimeExtensionInterface
{
    /**
     * @param string $locale
     *
     * @return string
     */
    public function checkoutPaymentLocale(string $locale): string
    {
        $smallLocale = strtolower(substr($locale, 0, 2));

        switch ($smallLocale) {
            case ('ar'):
                return 'AR';
            case ('da'):
                return 'DA-DK';
            case ('nl'):
                return 'NL-NL';
            case ('en'):
                return 'EN-GB';
            case ('fi'):
                return 'FI-FI';
            case ('fr'):
                return 'FR-FR';
            case ('de'):
                return 'DE-DE';
            case ('hi'):
                return 'HI-IN';
            case ('id'):
                return 'ID-ID';
            case ('it'):
                return 'IT-IT';
            case ('ja'):
                return 'JA-JP';
            case ('ko'):
                return 'KO-KR';
            case ('ms'):
                return 'MS-MY';
            case ('nb'):
            case ('no'):
                return 'NB-NO';
            case ('es'):
                return 'ES-ES';
            case ('sv'):
                return 'SV-SE';
            case ('th'):
                return 'TH-TH';
            case ('vi'):
                return 'VI-VN';
            default:
                return 'EN-GB';
        }
    }
}
