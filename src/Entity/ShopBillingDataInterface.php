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

use Sylius\Component\Resource\Model\ResourceInterface;

interface ShopBillingDataInterface extends ResourceInterface
{
    /**
     * @return mixed
     */
    public function getId();

    public function getCompany(): ?string;

    public function setCompany(?string $company): void;

    public function getTaxId(): ?string;

    public function setTaxId(?string $taxId): void;

    public function getCountryCode(): ?string;

    public function setCountryCode(?string $countryCode): void;

    public function getStreet(): ?string;

    public function setStreet(?string $street): void;

    public function getCity(): ?string;

    public function setCity(?string $city): void;

    public function getPostcode(): ?string;

    public function setPostcode(?string $postcode): void;

    /**
     * @return mixed
     *
     * @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getId() instead
     */
    public function id();

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getCompany() instead */
    public function company(): ?string;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getTaxId() instead */
    public function taxId(): ?string;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getCountryCode() instead */
    public function countryCode(): ?string;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getStreet() instead */
    public function street(): ?string;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getCity() instead */
    public function city(): ?string;

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use ShopBillingDataInterface::getPostcode() instead */
    public function postcode(): ?string;
}
