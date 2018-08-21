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

    public function hasCreditMemoWithData(
        int $index,
        string $orderNumber,
        string $total,
        \DateTimeInterface $issuedAt
    ): bool {
        $creditMemoData = $this->getDocument()->findAll('css', 'table thead tr')[0]->getText();

        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getDocument()->findAll('css', 'table tbody tr')[$index-1];

        return
            sprintf($creditMemoData, ['Number', 'Order number', 'Total', 'Issued at', 'Actions']) !== null &&
            $creditMemo->find('css', 'td:nth-child(4)')->getText() !== '' &&
            $creditMemo->find('css', sprintf('td:contains("%s")', $issuedAt->format('Y-m-d H:i:s'))) !== null &&
            $creditMemo->find('css', sprintf('td:contains("%s")', $total)) !== null
        ;
    }
}
