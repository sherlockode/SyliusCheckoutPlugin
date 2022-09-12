<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Model;

class Charge
{
    public const STATE_AUTHORIZED = 'Authorized';
    public const STATE_PENDING = 'Pending';
    public const STATE_VERIFIED = 'Card Verified';
    public const STATE_CAPTURED = 'Captured';
    public const STATE_DECLINED = 'Declined';
    public const STATE_PAID = 'Paid';

    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $token;

    /**
     * @var string
     */
    private $instrument;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int|null
     */
    private $amount;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * @var bool|null
     */
    private $capture;

    /**
     * @var string|null
     */
    private $successUrl;

    /**
     * @var string|null
     */
    private $failureUrl;

    /**
     * @var array[]
     */
    private $links;

    /**
     * @var string
     */
    private $customerId;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $paymentIpAddress;

    /**
     * Charge constructor.
     */
    public function __construct()
    {
        $this->links = [];
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInstrument(): ?string
    {
        return $this->instrument;
    }

    /**
     * @param string $instrument
     *
     * @return $this
     */
    public function setInstrument(string $instrument): self
    {
        $this->instrument = $instrument;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCapture(): ?bool
    {
        return $this->capture;
    }

    /**
     * @param bool $capture
     *
     * @return $this
     */
    public function setCapture(bool $capture): self
    {
        $this->capture = $capture;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSuccessUrl(): ?string
    {
        return $this->successUrl;
    }

    /**
     * @param string $successUrl
     *
     * @return $this
     */
    public function setSuccessUrl(string $successUrl): self
    {
        $this->successUrl = $successUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFailureUrl(): ?string
    {
        return $this->failureUrl;
    }

    /**
     * @param string $failureUrl
     *
     * @return $this
     */
    public function setFailureUrl(string $failureUrl): self
    {
        $this->failureUrl = $failureUrl;

        return $this;
    }

    /**
     * @return array[]
     */
    public function getLinks(): ?array
    {
        return $this->links;
    }

    /**
     * @param array[] $links
     *
     * @return $this
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     *
     * @return $this
     */
    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     *
     * @return $this
     */
    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    /**
     * @param string $customerEmail
     *
     * @return $this
     */
    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     *
     * @return $this
     */
    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentIpAddress(): ?string
    {
        return $this->paymentIpAddress;
    }

    /**
     * @param string|null $paymentIpAddress
     *
     * @return $this
     */
    public function setPaymentIpAddress(?string $paymentIpAddress): self
    {
        $this->paymentIpAddress = $paymentIpAddress;

        return $this;
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, [self::STATE_AUTHORIZED, self::STATE_CAPTURED, self::STATE_PAID]);
    }

    /**
     * @return bool
     */
    public function hasRedirection(): bool
    {
        return null !== $this->getRedirectionUrl();
    }

    /**
     * @return string|null
     */
    public function getRedirectionUrl(): ?string
    {
        return $this->links['redirect']['href'] ?? null;
    }
}
