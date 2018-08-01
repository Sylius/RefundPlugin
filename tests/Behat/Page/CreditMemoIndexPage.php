<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage;

final class CreditMemoIndexPage extends IndexPage implements CreditMemoIndexPageInterface
{
    public function hasCreditMemoWithData(int $index, string $orderNumber, string $total): bool
    {
        /** @var NodeElement $creditMemo */
        $creditMemo = $this->getDocument()->findAll('css', 'table tr')[$index-1];

        return
            $creditMemo->find('css', sprintf('td:contains("%s")', $orderNumber)) !== null &&
            $creditMemo->find('css', sprintf('td:contains("%s")', $total)) !== null
        ;
    }
}
