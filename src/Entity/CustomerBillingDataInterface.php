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

interface CustomerBillingDataInterface
{
    public function id(): ?int;

    public function firstName(): string;

    public function lastName(): string;

    public function fullName(): string;

    public function street(): string;

    public function postcode(): string;

    public function countryCode(): string;

    public function city(): string;

    public function company(): ?string;

    public function provinceName(): ?string;

    public function provinceCode(): ?string;
}
