<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;

interface CreditMemoIndexPageInterface extends IndexPageInterface
{
    public function downloadCreditMemo(int $index): void;

    public function hasCreditMemoWithData(
        int $index,
        string $orderNumber,
        string $total,
        \DateTimeInterface $issuedAt
    ): bool;

    public function hasCreditMemoWithChannel(int $index, string $channelName): bool;
}
