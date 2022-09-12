<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Model;

class Instrument
{
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
    private $customerId;

    /**
     * @var int
     */
    private $expiresMonth;

    /**
     * @var int
     */
    private $expiresYear;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $last4;

    /**
     * @var Address
     */
    private $address;

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
     * @return int|null
     */
    public function getExpiresMonth(): ?int
    {
        return $this->expiresMonth;
    }

    /**
     * @param int $expiresMonth
     *
     * @return $this
     */
    public function setExpiresMonth(int $expiresMonth): self
    {
        $this->expiresMonth = $expiresMonth;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpiresYear(): ?int
    {
        return $this->expiresYear;
    }

    /**
     * @param int $expiresYear
     *
     * @return $this
     */
    public function setExpiresYear(int $expiresYear): self
    {
        $this->expiresYear = $expiresYear;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     *
     * @return $this
     */
    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLast4(): ?string
    {
        return $this->last4;
    }

    /**
     * @param string $last4
     *
     * @return $this
     */
    public function setLast4(string $last4): self
    {
        $this->last4 = $last4;

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
}
