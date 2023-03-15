<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout;

use Sylius\Component\Locale\Context\LocaleContextInterface;

class LocaleProvider
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        $smallLocale = strtolower(substr($this->localeContext->getLocaleCode(), 0, 2));
        $localeMap = [
            'ar' => 'AR',
            'da' => 'DA-DK',
            'nl' => 'NL-NL',
            'fi' => 'FI-FI',
            'fr' => 'FR-FR',
            'de' => 'DE-DE',
            'hi' => 'HI-IN',
            'id' => 'ID-ID',
            'it' => 'IT-IT',
            'ja' => 'JA-JP',
            'ko' => 'KO-KR',
            'ms' => 'MS-MY',
            'nb' => 'NB-NO',
            'no' => 'NB-NO',
            'es' => 'ES-ES',
            'sv' => 'SV-SE',
            'th' => 'TH-TH',
            'vi' => 'VI-VN',
        ];

        return $localeMap[$smallLocale] ?? 'EN-GB';
    }
}
