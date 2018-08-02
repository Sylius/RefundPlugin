<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CreditMemoInterface extends ResourceInterface
{
    public function getNumber(): string;

    public function getOrderNumber(): string;

    public function getTotal(): int;

    public function getCurrencyCode(): string;

    public function getUnits(): array;
}
