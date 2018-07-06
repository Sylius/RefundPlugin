<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;

final class OrderRefundsPage extends SymfonyPage implements OrderRefundsPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_refund_order_refunds_list';
    }

    public function countRefundableUnitsWithProduct(string $productName): int
    {
        return count($this->getUnitsWithProduct($productName));
    }

    public function getRefundedTotal(): string
    {
        return str_replace('Refunded total: ', '', $this->getDocument()->find('css', '#refundedTotal')->getText());
    }

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void
    {
        $units = $this->getUnitsWithProduct($productName);

        $units[$unitNumber]->find('css', '.checkbox input')->check();
    }

    public function refund(): void
    {
        $this->getDocument()->pressButton('Refund');
    }

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool
    {
        $units = $this->getUnitsWithProduct($productName);

        return !$units[$unitNumber]->hasClass('disabled');
    }

    /** @return array|NodeElement[] */
    private function getUnitsWithProduct(string $productName): array
    {
        return $this->getDocument()->findAll('css', sprintf('#refunds .unit:contains("%s")', $productName));
    }
}
