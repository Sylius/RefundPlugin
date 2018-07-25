<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoInterface
{
    public function getId(): int;

    public function getNumber(): string;

    public function getOrderNumber(): string;

    public function getTotal(): int;

    public function getCurrencyCode(): string;

    public function getUnits(): array;
}
