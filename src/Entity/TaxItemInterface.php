<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface TaxItemInterface
{
    public function getLabel(): string;

    public function getAmount(): int;

    public function serialize(): string;
}
