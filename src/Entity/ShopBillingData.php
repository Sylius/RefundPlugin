<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** final */
class ShopBillingData implements ShopBillingDataInterface
{
    /** @var mixed */
    protected $id;

    protected ?string $company = null;

    protected ?string $taxId = null;

    protected ?string $countryCode = null;

    protected ?string $street = null;

    protected ?string $city = null;

    protected ?string $postcode = null;

    public function getId()
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getId() instead */
    public function id()
    {
        return $this->id;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getCompany() instead */
    public function company(): ?string
    {
        return $this->company;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getTaxId() instead */
    public function taxId(): ?string
    {
        return $this->taxId;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getCountryCode() instead */
    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getStreet() instead */
    public function street(): ?string
    {
        return $this->street;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getCity() instead */
    public function city(): ?string
    {
        return $this->city;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingData::getPostcode() instead */
    public function postcode(): ?string
    {
        return $this->postcode;
    }
}
