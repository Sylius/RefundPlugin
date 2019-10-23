<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** final */
class ShopBillingData implements ShopBillingDataInterface
{
    /** @var int */
    protected $id;

    /** @var string|null */
    protected $company;

    /** @var string|null */
    protected $taxId;

    /** @var string|null */
    protected $countryCode;

    /** @var string|null */
    protected $street;

    /** @var string|null */
    protected $city;

    /** @var string|null */
    protected $postcode;

    public function __construct(
        ?string $company,
        ?string $taxId,
        ?string $countryCode,
        ?string $street,
        ?string $city,
        ?string $postcode
    ) {
        $this->company = $company;
        $this->taxId = $taxId;
        $this->countryCode = $countryCode;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function company(): ?string
    {
        return $this->company;
    }

    public function taxId(): ?string
    {
        return $this->taxId;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function street(): ?string
    {
        return $this->street;
    }

    public function city(): ?string
    {
        return $this->city;
    }

    public function postcode(): ?string
    {
        return $this->postcode;
    }
}
