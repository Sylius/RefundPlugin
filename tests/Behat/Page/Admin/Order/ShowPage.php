<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Order\ShowPage as BaseOrderShowPage;

final class ShowPage extends BaseOrderShowPage implements ShowPageInterface
{
    public function countCreditMemos(): int
    {
        return count($this->getDocument()->findAll('css', '#credit-memos tbody tr'));
    }

    public function downloadCreditMemo(int $index): void
    {
        /** @var NodeElement $creditMemoRow */
        $creditMemoRow = $this->getDocument()->findAll('css', '#credit-memos tbody tr')[$index-1];

        $creditMemoRow->clickLink('Download');
    }

    public function completeRefundPayment(int $number): void
    {
        $refundPayments = $this->getDocument()->findAll('css', '#refund-payments tbody tr');

        /** @var NodeElement $refundPayment */
        $refundPayment = $refundPayments[$number];

        $refundPayment->pressButton('Complete');
    }

    public function countRefundPayment(): int
    {
        return count($this->getDocument()->findAll('css', '#refund-payments tbody tr'));
    }

    public function hasRefundsButton(): bool
    {
        return $this->getDocument()->hasButton('Refunds');
    }

    public function hasRefundPaymentsWithStatus(int $count, string $status): bool
    {
        $refundPayments = $this->getDocument()->findAll('css', '#refund-payments tbody tr');

        $refundPaymentsWithStatus = 0;
        /** @var NodeElement $refundPayment */
        foreach ($refundPayments as $refundPayment) {
            if (strpos($refundPayment->getText(), $status)) {
                $refundPaymentsWithStatus++;
            }
        }

        return $count === $refundPaymentsWithStatus;
    }

    public function canCompleteRefundPayment(int $number): bool
    {
        $refundPayments = $this->getDocument()->findAll('css', '#refund-payments tbody tr');

        /** @var NodeElement $refundPayment */
        $refundPayment = $refundPayments[$number];

        return $refundPayment->hasButton('Complete');
    }
}
