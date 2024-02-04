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

/** @final */
class CustomerBillingData implements CustomerBillingDataInterface
{
    /** @var int|null */
    protected $id;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

    protected ?string $street = null;

    protected ?string $postcode = null;

    protected ?string $countryCode = null;

    protected ?string $city = null;

    protected ?string $company = null;

    protected ?string $provinceName = null;

    protected ?string $provinceCode = null;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getId() instead */
    public function id()
    {
        return $this->id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getFirstName() instead */
    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getLastName() instead */
    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getFullName() instead */
    public function fullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getStreet() instead */
    public function street(): ?string
    {
        return $this->street;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getPostcode() instead */
    public function postcode(): ?string
    {
        return $this->postcode;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getCountryCode() instead */
    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getCity() instead */
    public function city(): ?string
    {
        return $this->city;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getCompany() instead */
    public function company(): ?string
    {
        return $this->company;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getProvinceName() instead */
    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    public function getProvinceName(): ?string
    {
        return $this->provinceName;
    }

    public function setProvinceName(?string $provinceName): void
    {
        $this->provinceName = $provinceName;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use CustomerBillingData::getProvinceCode() instead */
    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function setProvinceCode(?string $provinceCode): void
    {
        $this->provinceCode = $provinceCode;
    }
}
