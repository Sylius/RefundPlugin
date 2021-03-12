<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface CreditMemoDetailsPageInterface extends SymfonyPageInterface
{
    public function hasItem(
        int $quantity,
        string $productName,
        string $netValue,
        string $grossValue,
        string $taxAmount,
        string $currencyCode
    ): bool;

    public function hasTaxItem(string $label, string $amount, string $currencyCode): bool;

    public function download(): void;

    public function getNumber(): string;

    public function getChannelName(): string;

    public function getTotal(): string;

    public function getTotalCurrencyCode(): string;

    public function getComment(): string;

    public function getFromAddress(): string;

    public function getToAddress(): string;

    public function isCreditMemoInPosition(string $creditMemo, int $position): bool;

    public function getNetTotal(): string;

    public function getTaxTotal(): string;
}
