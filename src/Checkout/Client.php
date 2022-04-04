<?php

namespace Sherlockode\SyliusCheckoutPlugin\Checkout;

use Checkout\CheckoutApiException;
use Checkout\CheckoutArgumentException;
use Checkout\CheckoutFourSdk;
use Checkout\Customers\Four\CustomerRequest;
use Checkout\Environment;
use Checkout\Four\CheckoutApi;
use Checkout\Instruments\Four\Create\CreateCustomerInstrumentRequest;
use Checkout\Instruments\Four\Create\CreateTokenInstrumentRequest;
use Checkout\Payments\Four\Request\PaymentRequest;
use Checkout\Payments\Four\Request\Source\RequestIdSource;
use Checkout\Payments\Four\Request\Source\RequestTokenSource;
use Checkout\Payments\RefundRequest;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Charge;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Customer;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Instrument;
use Sherlockode\SyliusCheckoutPlugin\Checkout\Model\Refund;

/**
 * Class Client
 */
class Client
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
     * @var CheckoutApi
     */
    private $client;

    /**
     * Client constructor.
     *
     * @param string $publicKey
     * @param string $secretKey
     * @param bool   $production
     */
    public function __construct(string $publicKey, string $secretKey, bool $production)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->production = $production;
    }

    /**
     * @param Charge $charge
     *
     * @throws CheckoutArgumentException
     */
    public function createCharge(Charge $charge): void
    {
        if ($charge->getToken()) {
            $source = new RequestTokenSource();
            $source->token = $charge->getToken();
        } elseif ($charge->getInstrument())  {
            $source = new RequestIdSource();
            $source->id = $charge->getInstrument();
        } else {
            return;
        }

        $request = new PaymentRequest();
        $request->source = $source;
        $request->amount = $charge->getAmount();
        $request->currency = $charge->getCurrency();
        $request->reference = $charge->getReference();
        $request->authorization_type = 'Final';
        $request->capture = $charge->isCapture();
        $request->success_url = $charge->getSuccessUrl();
        $request->failure_url = $charge->getFailureUrl();

        try {
            $payment = $this->getClient()->getPaymentsClient()->requestPayment($request);
        } catch (CheckoutApiException $exception) {
            return;
        }

        $charge->setId($payment['id']);
        $charge->setStatus($payment['status']);
        $charge->setLinks($payment['_links']);
    }

    /**
     * @param string $id
     *
     * @return Charge|null
     *
     * @throws CheckoutArgumentException
     */
    public function getCharge(string $id): ?Charge
    {
        try {
            $payment = $this->getClient()->getPaymentsClient()->getPaymentDetails($id);
        } catch (CheckoutApiException $exception) {
            return null;
        }

        $charge = new Charge();
        $charge->setId($payment['id']);
        $charge->setStatus($payment['status']);
        $charge->setLinks($payment['_links']);

        return $charge;
    }

    /**
     * @param Refund $refund
     *
     * @throws CheckoutArgumentException
     */
    public function refund(Refund $refund): void
    {
        $request = new RefundRequest();
        $request->reference = $refund->getReference();
        $request->amount = $refund->getAmount();

        try {
            $response = $this->getClient()->getPaymentsClient()->refundPayment($refund->getPaymentId(), $request);
        } catch (CheckoutApiException $exception) {
            return;
        }

        if (isset($response['action_id'])) {
            $refund->setId($response['action_id']);
        }
    }

    /**
     * @param Instrument $instrument
     *
     * @throws CheckoutArgumentException
     */
    public function createInstrument(Instrument $instrument): void
    {
        $customer = new CreateCustomerInstrumentRequest();
        $customer->id = $instrument->getCustomerId();

        $request = new CreateTokenInstrumentRequest();
        $request->type = 'token';
        $request->token = $instrument->getToken();
        $request->customer = $customer;

        try {
            $response = $this->getClient()->getInstrumentsClient()->create($request);
        } catch (CheckoutApiException $exception) {
            return;
        }

        if (isset($response['id'])) {
            $instrument->setId($response['id']);
        }
    }

    /**
     * @param string $id
     *
     * @return Customer
     *
     * @throws CheckoutArgumentException
     */
    public function getCustomer(string $id): ?Customer
    {
        try {
            $response = $this->getClient()->getCustomersClient()->get($id);
        } catch (CheckoutApiException $exception) {
            return null;
        }

        $customer = new Customer();
        $customer->setId($response['id']);
        $customer->setFullName($response['name']);
        $customer->setEmail($response['email']);

        foreach ($response['instruments'] as $row) {
            $instrument = new Instrument();
            $instrument->setId($row['id']);
            $instrument->setCustomerId($response['id']);
            $instrument->setExpiresMonth($row['expiry_month']);
            $instrument->setExpiresYear($row['expiry_year']);
            $instrument->setScheme($row['scheme']);
            $instrument->setLast4($row['last4']);
            $customer->addInstrument($instrument);
        }

        return $customer;
    }

    /**
     * @param Customer $customer
     *
     * @throws CheckoutArgumentException
     */
    public function createCustomer(Customer $customer): void
    {
        $request = new CustomerRequest();
        $request->email = $customer->getEmail();
        $request->name = $customer->getFullName();

        try {
            $response = $this->getClient()->getCustomersClient()->create($request);
        } catch (CheckoutApiException $exception) {
            return;
        }

        $customer->setId($response['id']);
    }

    /**
     * @return CheckoutApi
     *
     * @throws CheckoutArgumentException
     */
    private function getClient(): CheckoutApi
    {
        if ($this->client) {
            return $this->client;
        }

        $builder = CheckoutFourSdk::staticKeys();
        $builder->setPublicKey($this->publicKey);
        $builder->setSecretKey($this->secretKey);
        $builder->setEnvironment($this->production ? Environment::production() : Environment::sandbox());
        $this->client = $builder->build();

        return $this->client;
    }
}
