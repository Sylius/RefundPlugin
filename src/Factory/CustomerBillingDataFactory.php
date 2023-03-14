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

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Webmozart\Assert\Assert;

class CustomerBillingDataFactory implements CustomerBillingDataFactoryInterface
{
    private FactoryInterface $customerBillingDataFactory;

    public function __construct(FactoryInterface $customerBillingDataFactory)
    {
        $this->customerBillingDataFactory = $customerBillingDataFactory;
    }

    public function createNew(): CustomerBillingDataInterface
    {
        /** @var CustomerBillingDataInterface $customerBillingData */
        $customerBillingData = $this->customerBillingDataFactory->createNew();

        return $customerBillingData;
    }

    public function createWithData(
        string $firstName,
        string $lastName,
        string $street,
        string $postcode,
        string $countryCode,
        string $city,
        ?string $company = null,
        ?string $provinceName = null,
        ?string $provinceCode = null,
    ): CustomerBillingDataInterface {
        $customerBillingData = $this->createNew();

        $customerBillingData->setFirstName($firstName);
        $customerBillingData->setLastName($lastName);
        $customerBillingData->setStreet($street);
        $customerBillingData->setPostcode($postcode);
        $customerBillingData->setCountryCode($countryCode);
        $customerBillingData->setCity($city);
        $customerBillingData->setCompany($company);
        $customerBillingData->setProvinceName($provinceName);
        $customerBillingData->setProvinceCode($provinceCode);

        return $customerBillingData;
    }

    public function createWithAddress(AddressInterface $address): CustomerBillingDataInterface
    {
        Assert::notNull($address->getFirstName());
        Assert::notNull($address->getLastName());
        Assert::notNull($address->getStreet());
        Assert::notNull($address->getPostcode());
        Assert::notNull($address->getCountryCode());
        Assert::notNull($address->getCity());

        return $this->createWithData(
            $address->getFirstName(),
            $address->getLastName(),
            $address->getStreet(),
            $address->getPostcode(),
            $address->getCountryCode(),
            $address->getCity(),
            $address->getCompany(),
            $address->getProvinceName(),
            $address->getProvinceCode(),
        );
    }
}
