<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Sylius\Behat\Page\SymfonyPage;

final class CreditMemoDetailsPage extends SymfonyPage implements CreditMemoDetailsPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_refund_credit_memo_details';
    }

    public function countUnitsWithProduct(string $productName): int
    {
        // TODO: Implement countUnitsWithProduct() method.
    }

    public function getUnitDiscount(int $number, string $productName): string
    {
        // TODO: Implement getUnitDiscount() method.
    }

    public function getUnitTax(int $number, string $productName): string
    {
        // TODO: Implement getUnitTax() method.
    }

    public function getNumber(): string
    {
        return str_replace('#', '', $this->getDocument()->find('css', '#number')->getText());
    }

    public function getTotal(): string
    {
        return str_replace('Total: ', '', $this->getDocument()->find('css', '#total')->getText());
    }
}
