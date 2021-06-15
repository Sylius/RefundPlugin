<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerBillingDataInterface extends ResourceInterface
{
    public function setId(?int $id): void;

    public function getFirstName(): string;

    public function setFirstName(string $firstName): void;

    public function getLastName(): string;

    public function setLastName(string $lastName): void;

    public function getFullName(): string;

    public function getStreet(): string;

    public function setStreet(string $street): void;

    public function getPostcode(): string;

    public function setPostcode(string $postcode): void;

    public function getCountryCode(): string;

    public function setCountryCode(string $countryCode): void;

    public function getCity(): string;

    public function setCity(string $city): void;

    public function getCompany(): ?string;

    public function setCompany(?string $company): void;

    public function getProvinceName(): ?string;

    public function setProvinceName(?string $provinceName): void;

    public function getProvinceCode(): ?string;

    public function setProvinceCode(?string $provinceCode): void;
}
