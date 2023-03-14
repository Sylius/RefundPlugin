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

interface CustomerBillingDataFactoryInterface extends FactoryInterface
{
    public function createWithData(
        string $firstName,
        string $lastName,
        string $street,
        string $postcode,
        string $countryCode,
        string $city,
        ?string $company,
        ?string $provinceName,
        ?string $provinceCode,
    ): CustomerBillingDataInterface;

    public function createWithAddress(AddressInterface $address): CustomerBillingDataInterface;
}
