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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

interface ShopBillingDataFactoryInterface extends FactoryInterface
{
    public function createWithData(
        ?string $company,
        ?string $taxId,
        ?string $countryCode,
        ?string $street,
        ?string $city,
        ?string $postcode,
    ): ShopBillingDataInterface;
}
