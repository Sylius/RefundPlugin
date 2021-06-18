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

use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

final class ShopBillingDataFactory implements ShopBillingDataFactoryInterface
{
    public function createNew(): ShopBillingDataInterface
    {
        throw new UnsupportedMethodException('This object is not default constructable.');
    }

    public function createWithData(
        ?string $company,
        ?string $taxId,
        ?string $countryCode,
        ?string $street,
        ?string $city,
        ?string $postcode
    ): ShopBillingDataInterface {
        $shopBillingData = new ShopBillingData();

        $shopBillingData->setCompany($company);
        $shopBillingData->setTaxId($taxId);
        $shopBillingData->setCountryCode($countryCode);
        $shopBillingData->setStreet($street);
        $shopBillingData->setCity($city);
        $shopBillingData->setPostcode($postcode);

        return $shopBillingData;
    }
}
