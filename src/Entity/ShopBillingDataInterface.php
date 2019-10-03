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

interface ShopBillingDataInterface
{
    public function id(): int;

    public function company(): ?string;

    public function taxId(): ?string;

    public function countryCode(): ?string;

    public function street(): ?string;

    public function city(): ?string;

    public function postcode(): ?string;
}
