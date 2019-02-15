<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

final class ShopBillingData
{
    /** @var string */
    private $company;

    /** @var string */
    private $taxId;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var string */
    private $postcode;

    public function __construct(
        string $company,
        string $taxId,
        string $countryCode,
        string $street,
        string $city,
        string $postcode
    ) {
        $this->company = $company;
        $this->taxId = $taxId;
        $this->countryCode = $countryCode;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
    }

    public function company(): string
    {
        return $this->company;
    }

    public function taxId(): string
    {
        return $this->taxId;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function postcode(): string
    {
        return $this->postcode;
    }
}
