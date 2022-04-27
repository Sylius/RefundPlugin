<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElementInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    private ShowPageInterface $customerOrderShowPage;

    private PdfDownloadElementInterface $pdfDownloadElement;

    public function __construct(
        ShowPageInterface $customerOrderShowPage,
        PdfDownloadElementInterface $pdfDownloadElement
    ) {
        $this->customerOrderShowPage = $customerOrderShowPage;
        $this->pdfDownloadElement = $pdfDownloadElement;
    }

    /**
     * @Then there should be :count credit memo(s) related to this order
     */
    public function thereShouldBeCountCreditMemoRelatedToThisOrder(int $count): void
    {
        Assert::same($this->customerOrderShowPage->countCreditMemos(), $count);
    }

    /**
     * @Then a pdf file should be successfully downloaded
     */
    public function pdfFileShouldBeSuccessfullyDownloaded(): void
    {
        Assert::true($this->pdfDownloadElement->isPdfFileDownloaded());
    }

    /**
     * @When I download the first credit memo
     */
    public function downloadFirstCreditMemo(): void
    {
        $this->customerOrderShowPage->downloadCreditMemo(1);
    }

    /**
     * @When I should not be able to download the first credit memo
     */
    public function iShouldNotBeAbleToDownloadTheFirstCreditMemo(): void
    {
        $this->customerOrderShowPage->hasDownloadCreditMemoButton(1);
    }
}
