<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order;

use Sylius\Behat\Page\Shop\Order\ShowPageInterface as BaseOrderShowPageInterface;

interface ShowPageInterface extends BaseOrderShowPageInterface
{
    public function countCreditMemos(): int;

    public function downloadCreditMemo(int $index): void;

    public function hasDownloadCreditMemoButton(int $index): bool;
}
