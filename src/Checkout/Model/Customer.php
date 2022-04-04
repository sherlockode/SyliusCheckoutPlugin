<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout\Model;

class Customer
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Instrument[]
     */
    private $instruments;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->instruments = [];
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
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return $this
     */
    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Instrument[]
     */
    public function getInstruments(): array
    {
        return $this->instruments;
    }

    /**
     * @param Instrument $instrument
     *
     * @return $this
     */
    public function addInstrument(Instrument $instrument): self
    {
        $this->instruments[] = $instrument;

        return $this;
    }
}
