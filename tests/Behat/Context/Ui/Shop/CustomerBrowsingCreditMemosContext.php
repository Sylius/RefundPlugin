<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class CustomerBrowsingCreditMemosContext implements Context
{
    /** @var ShowPageInterface */
    private $customerOrderShowPage;

    public function __construct(ShowPageInterface $customerOrderShowPage)
    {
        $this->customerOrderShowPage = $customerOrderShowPage;
    }

    /**
     * @Then I should see :count credit memo(s) related to this order
     */
    public function seeCreditMemoRelatedToThisOrder(int $count): void
    {
        Assert::same($this->customerOrderShowPage->countCreditMemos(), $count);
    }
}
