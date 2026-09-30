<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElementInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    public function __construct(
        private readonly ShowPageInterface $customerOrderShowPage,
        private readonly PdfDownloadElementInterface $pdfDownloadElement
    ) {
    }

    #[Then('there should be :count credit memo(s) related to this order')]
    public function thereShouldBeCountCreditMemoRelatedToThisOrder(int $count): void
    {
        Assert::same($this->customerOrderShowPage->countCreditMemos(), $count);
    }

    #[Then('a pdf file should be successfully downloaded')]
    public function pdfFileShouldBeSuccessfullyDownloaded(): void
    {
        Assert::true($this->pdfDownloadElement->isPdfFileDownloaded());
    }

    #[When('I download the first credit memo')]
    public function downloadFirstCreditMemo(): void
    {
        $this->customerOrderShowPage->downloadCreditMemo(0);
    }

    #[When('I should not be able to download the first credit memo')]
    public function iShouldNotBeAbleToDownloadTheFirstCreditMemo(): void
    {
        $this->customerOrderShowPage->hasDownloadCreditMemoButton(0);
    }
}
