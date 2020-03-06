<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CustomerBillingData implements CustomerBillingDataInterface
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $customerName;

    /** @var string */
    protected $street;

    /** @var string */
    protected $postcode;

    /** @var string */
    protected $countryCode;

    /** @var string */
    protected $city;

    /** @var string|null */
    protected $company;

    /** @var string|null */
    protected $provinceName;

    /** @var string|null */
    protected $provinceCode;

    public function __construct(
        string $customerName,
        string $street,
        string $postcode,
        string $countryCode,
        string $city,
        ?string $company = null,
        ?string $provinceName = null,
        ?string $provinceCode = null
    ) {
        $this->customerName = $customerName;
        $this->street = $street;
        $this->postcode = $postcode;
        $this->countryCode = $countryCode;
        $this->city = $city;
        $this->company = $company;
        $this->provinceName = $provinceName;
        $this->provinceCode = $provinceCode;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function customerName(): string
    {
        return $this->customerName;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function postcode(): string
    {
        return $this->postcode;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function company(): ?string
    {
        return $this->company;
    }

    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }
}
