<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;

interface CreditMemoIndexPageInterface extends IndexPageInterface
{
    public function downloadCreditMemo(int $index): void;

    public function filterByChannel(string $channelName): void;

    public function resendCreditMemo(string $orderNumber): void;

    public function hasCreditMemoWithOrderNumber(int $index, string $orderNumber): bool;

    public function hasCreditMemoWithDateOfBeingIssued(int $index, \DateTimeInterface $issuedAt): bool;

    public function hasCreditMemoWithTotal(int $index, string $total): bool;

    public function hasCreditMemoWithChannel(int $index, string $channelName): bool;

    public function hasSingleCreditMemoForOrder(string $orderNumber): bool;

    public function hasDownloadButton(int $index): bool;
}
