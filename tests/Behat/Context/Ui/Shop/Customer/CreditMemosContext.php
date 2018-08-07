<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class CreditMemosContext implements Context
{
    /** @var ShowPageInterface */
    private $customerOrderShowPage;

    public function __construct(ShowPageInterface $customerOrderShowPage)
    {
        $this->customerOrderShowPage = $customerOrderShowPage;
    }

    /**
     * @Then there should be :count credit memo(s) related to this order
     */
    public function thereShouldBeCountCreditMemoRelatedToThisOrder(int $count): void
    {
        Assert::same($this->customerOrderShowPage->countCreditMemos(), $count);
    }

    /**
     * @When I download the first credit memo
     */
    public function downloadFirstCreditMemo(): void
    {
        $this->customerOrderShowPage->downloadFirstCreditMemo();
    }

    /**
     * @Then the pdf file for this credit memo should be downloaded successfully
     */
    public function pdfFileForThisCreditMemoShouldBeDownloadedSuccessfully(): void
    {
        Assert::true($this->customerOrderShowPage->isPdfFileDownloaded());
    }
}
