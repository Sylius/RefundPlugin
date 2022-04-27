<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage;

final class CreditMemoIndexPage extends IndexPage implements CreditMemoIndexPageInterface
{
    public function downloadCreditMemo(int $index): void
    {
        /** @var NodeElement $creditMemoRow */
        $creditMemoRow = $this->getDocument()->findAll('css', 'tbody tr')[$index-1];

        $creditMemoRow->clickLink('Download');
    }

    public function filterByChannel(string $channelName): void
    {
        $this->getDocument()->find('css', '#criteria_channel')->selectOption($channelName);
    }

    public function hasCreditMemoWithOrderNumber(int $index, string $orderNumber): bool
    {
        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getCreditMemoElement($index);

        return $creditMemo->find('css', sprintf('td:contains("%s")', $orderNumber)) !== null;
    }

    public function hasCreditMemoWithDateOfBeingIssued(int $index, \DateTimeInterface $issuedAt): bool
    {
        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getCreditMemoElement($index);

        return $creditMemo->find('css', sprintf('td:contains("%s")', $issuedAt->format('Y-m-d H:i:s'))) !== null;
    }

    public function hasCreditMemoWithTotal(int $index, string $total): bool
    {
        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getCreditMemoElement($index);

        return $creditMemo->find('css', sprintf('td:contains("%s")', $total)) !== null;
    }

    public function hasCreditMemoWithChannel(int $index, string $channelName): bool
    {
        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getCreditMemoElement($index);

        return $creditMemo->find('css', sprintf('td:contains("%s")', $channelName)) !== null;
    }

    public function hasSingleCreditMemoForOrder(string $orderNumber): bool
    {
        $creditMemos = $this->getDocument()->findAll('css', 'table tbody tr');

        return
            count($creditMemos) === 1 &&
            $creditMemos[0]->find('css', sprintf('td:contains("%s")', $orderNumber)) !== null
        ;
    }

    private function getCreditMemoElement(int $index): NodeElement
    {
        return $this->getDocument()->findAll('css', 'table tbody tr')[$index-1];
    }

    public function resendCreditMemo(string $orderNumber): void
    {
        $creditMemoRow = $this->getDocument()->find('css', sprintf('table tbody tr:contains("%s")', $orderNumber));
        $creditMemoRow->clickLink('Resend');
    }

    public function hasDownloadButton(int $index): bool
    {
        /** @var NodeElement $creditMemoRow */
        $creditMemoRow = $this->getDocument()->findAll('css', 'tbody tr')[$index-1];

        return $creditMemoRow->hasLink('Download');
    }
}
