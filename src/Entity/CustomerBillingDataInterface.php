<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CustomerBillingDataInterface
{
    public function customerName(): string;

    public function street(): string;

    public function postcode(): string;

    public function countryCode(): string;

    public function city(): string;

    public function company(): ?string;

    public function provinceName(): ?string;

    public function provinceCode(): ?string;
}
