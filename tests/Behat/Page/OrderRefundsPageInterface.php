<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Sylius\Behat\Page\SymfonyPageInterface;

interface OrderRefundsPageInterface extends SymfonyPageInterface
{
    public function countRefundableUnitsWithProduct(string $productName): int;
}
