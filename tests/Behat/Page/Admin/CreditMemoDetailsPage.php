<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class CreditMemoDetailsPage extends SymfonyPage implements CreditMemoDetailsPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_refund_credit_memo_details';
    }

    public function countUnitsWithProduct(string $productName): int
    {
        return count($this->getCreditMemoUnitsWithProduct($productName));
    }

    public function download(): void
    {
        $this->getDocument()->clickLink('Download');
    }

    public function getUnitTax(int $number, string $productName): string
    {
        $unit = $this->getCreditMemoUnitsWithProduct($productName)[0];

        return $unit->find('css', '.credit-memo-unit-taxes-total')->getText();
    }

    public function getUnitTotal(int $number, string $unitName): string
    {
        $unit = $this->getCreditMemoUnitsWithProduct($unitName)[0];

        return $unit->find('css', '.credit-memo-unit-total')->getText();
    }

    public function getNumber(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-number')->getText();
    }

    public function getChannelName(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-channel-name')->getText();
    }

    public function getTotal(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-total')->getText();
    }

    public function getComment(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-comment')->getText();
    }

    /** @return array|NodeElement[] */
    private function getCreditMemoUnitsWithProduct(string $productName): array
    {
        return $this->getDocument()->findAll('css', sprintf('tr:contains("%s")', $productName));
    }
}
