<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Order\ShowPage as BaseOrderShowPage;

final class ShowPage extends BaseOrderShowPage implements ShowPageInterface
{
    public function countCreditMemos(): int
    {
        return count($this->getDocument()->findAll('css', '#credit-memos tbody tr'));
    }

    public function downloadFirstCreditMemo(): void
    {
        $creditMemo = $this->getFirstCreditMemo();
        $creditMemo->clickLink('Download');
    }

    public function isPdfFileDownloaded(): bool
    {
        $session = $this->getSession();
        $headers = $session->getResponseHeaders();

        return
            200 === $session->getStatusCode() &&
            'application/pdf' === $headers['content-type'][0]
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'credit_memos' => '#credit-memos',
        ]);
    }

    private function getFirstCreditMemo(): NodeElement
    {
        return $this->getCreditMemosList()[1];
    }

    private function getCreditMemosList(): array
    {
        return $this->getElement('credit_memos')->findAll('css', 'tr');
    }
}
