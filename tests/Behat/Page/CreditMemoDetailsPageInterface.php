<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

interface CreditMemoDetailsPageInterface extends SymfonyPageInterface
{
    public function countUnitsWithProduct(string $productName): int;

    public function getUnitDiscount(int $number, string $productName): string;

    public function getUnitTax(int $number, string $productName): string;

    public function getTotal(): string;
}
