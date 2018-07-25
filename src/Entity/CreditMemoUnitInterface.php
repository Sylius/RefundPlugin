<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoUnitInterface
{
    public function getProductName(): string;

    public function getTotal(): int;

    public function getTaxesTotal(): int;

    public function serialize(): string;
}
