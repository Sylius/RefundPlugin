<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Sylius\Behat\Page\SymfonyPage;

final class OrderRefundsPage extends SymfonyPage implements OrderRefundsPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_refund_order_refunds';
    }

    public function countRefundableUnitsWithProduct(string $productName): int
    {
        return count($this->getDocument()->findAll('css', '#refunds .unit'));
    }
}
