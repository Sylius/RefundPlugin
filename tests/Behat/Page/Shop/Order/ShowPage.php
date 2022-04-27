<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order;

use Sylius\Behat\Page\Shop\Order\ShowPage as BaseOrderShowPage;

final class ShowPage extends BaseOrderShowPage implements ShowPageInterface
{
    public function countCreditMemos(): int
    {
        return count($this->getDocument()->findAll('css', '#credit-memos tbody tr'));
    }

    public function downloadCreditMemo(int $index): void
    {
        $creditMemo = $this->getCreditMemosList()[$index];
        $creditMemo->clickLink('Download');
    }

    public function hasDownloadCreditMemoButton(int $index): bool
    {
        $creditMemo = $this->getCreditMemosList()[$index];

        return $creditMemo->hasLink('Download');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'credit_memos' => '#credit-memos',
        ]);
    }

    private function getCreditMemosList(): array
    {
        return $this->getElement('credit_memos')->findAll('css', 'tr');
    }
}
