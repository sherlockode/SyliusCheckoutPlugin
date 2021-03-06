<?php

namespace Sherlockode\SyliusCheckoutPlugin\Payum;

class CheckoutApi
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var bool
     */
    private $production;

    /**
     * @var string|null
     */
    private $webhookSignature;

    /**
     * @var bool
     */
    private $retryDeclinedPayment;

    /**
     * CheckoutApi constructor.
     *
     * @param string      $publicKey
     * @param string      $secretKey
     * @param bool        $production
     * @param string|null $webhookSignature
     * @param bool        $retryDeclinedPayment
     */
    public function __construct(
        string $publicKey,
        string $secretKey,
        bool $production,
        ?string $webhookSignature = null,
        bool $retryDeclinedPayment = false
    ) {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->production = $production;
        $this->webhookSignature = $webhookSignature;
        $this->retryDeclinedPayment = $retryDeclinedPayment;
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param string|null $publicKey
     *
     * @return $this
     */
    public function setPublicKey(?string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    /**
     * @param string|null $secretKey
     *
     * @return $this
     */
    public function setSecretKey(?string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isProduction(): ?bool
    {
        return $this->production;
    }

    /**
     * @param bool|null $production
     *
     * @return $this
     */
    public function setProduction(?bool $production): self
    {
        $this->production = $production;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebhookSignature(): ?string
    {
        return $this->webhookSignature;
    }

    /**
     * @param string|null $webhookSignature
     *
     * @return $this
     */
    public function setWebhookSignature(?string $webhookSignature): self
    {
        $this->webhookSignature = $webhookSignature;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRetryDeclinedPayment(): bool
    {
        return $this->retryDeclinedPayment;
    }

    /**
     * @param bool $retryDeclinedPayment
     *
     * @return $this
     */
    public function setRetryDeclinedPayment(bool $retryDeclinedPayment): self
    {
        $this->retryDeclinedPayment = $retryDeclinedPayment;

        return $this;
    }
}
