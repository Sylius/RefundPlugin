<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Order\ShowPageInterface as BaseOrderShowPageInterface;

interface ShowPageInterface extends BaseOrderShowPageInterface
{
    public function countCreditMemos(): int;

    public function downloadCreditMemo(int $index): void;

    public function completeRefundPayment(int $number): void;

    public function countRefundPayment(): int;

    public function hasRefundsButton(): bool;

    public function hasRefundPaymentsWithStatus(int $count, string $status): bool;

    public function canCompleteRefundPayment(int $number): bool;
}
