<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Context\Element\PdfDownloadElementInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    /** @var ShowPageInterface */
    private $orderShowPage;

    /** @var CreditMemoIndexPageInterface */
    private $creditMemoIndexPage;

    /** @var CreditMemoDetailsPageInterface */
    private $creditMemoDetailsPage;

    /** @var PdfDownloadElementInterface */
    private $pdfDownloadElement;

    /** @var ObjectRepository */
    private $creditMemoRepository;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    public function __construct(
        ShowPageInterface $orderShowPage,
        CreditMemoIndexPageInterface $creditMemoIndexPage,
        CreditMemoDetailsPageInterface $creditMemoDetailsPage,
        PdfDownloadElementInterface $pdfDownloadElement,
        ObjectRepository $creditMemoRepository,
        CurrentDateTimeProviderInterface $currentDateTimeProvider
    ) {
        $this->orderShowPage = $orderShowPage;
        $this->creditMemoIndexPage = $creditMemoIndexPage;
        $this->creditMemoDetailsPage = $creditMemoDetailsPage;
        $this->pdfDownloadElement = $pdfDownloadElement;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
    }

    /**
     * @When I browse the details of the only credit memo generated for order :order
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(OrderInterface $order): void
    {
        $orderNumber = $order->getNumber();
        $creditMemo = $this->creditMemoRepository->findBy(['orderNumber' => $orderNumber])[0];

        $this->creditMemoDetailsPage->open(['orderNumber' => $orderNumber, 'id' => $creditMemo->getId()]);
    }

    /**
     * @When I browse credit memos
     */
    public function browseCreditMemos(): void
    {
        $this->creditMemoIndexPage->open();
    }

    /**
     * @When /^I download (\d+)(?:|st|nd|rd) credit memo$/
     */
    public function downloadCreditMemoFromIndex(int $index): void
    {
        $this->creditMemoIndexPage->downloadCreditMemo($index);
    }

    /**
     * @When /^I download (\d+)(?:|st|nd|rd) order's credit memo$/
     */
    public function downloadCreditMemoFromOrderShow(int $index): void
    {
        $this->orderShowPage->downloadCreditMemo($index);
    }

    /**
     * @When I download it
     */
    public function downloadCreditMemo(): void
    {
        $this->creditMemoDetailsPage->download();
    }

    /**
     * @Then I should have :count credit memo generated for order :order
     */
    public function shouldHaveCountCreditMemoGeneratedForOrder(int $count, OrderInterface $order): void
    {
        $this->orderShowPage->open(['id' => $order->getId()]);
        Assert::same($this->orderShowPage->countCreditMemos(), $count);
    }

    /**
     * @Then this credit memo should contain :count :productName product with :tax tax applied
     */
    public function thisCreditMemoShouldContainProductWithTaxApplied(
        int $count,
        string $productName,
        string $tax
    ): void {
        Assert::same($this->creditMemoDetailsPage->countUnitsWithProduct($productName), $count);
        Assert::same($this->creditMemoDetailsPage->getUnitTax($count, $productName), $tax);
    }

    /**
     * @Then it should have sequential number generated from current date
     */
    public function shouldHaveSequentialNumberGeneratedFromCurrentDate(): void
    {
        Assert::same(
            $this->creditMemoDetailsPage->getNumber(),
            $this->currentDateTimeProvider->now()->format('Y/m').'/'.'000000001'
        );
    }

    /**
     * @Then its total should be :total
     */
    public function creditMemoTotalShouldBe(string $total): void
    {
        Assert::same($this->creditMemoDetailsPage->getTotal(), $total);
    }

    /**
     * @Then there should be :count credit memos generated
     */
    public function thereShouldBeCreditMemosGenerated(int $count): void
    {
        Assert::same($this->creditMemoIndexPage->countItems(), $count);
    }

    /**
     * @Then /^(\d+)(?:st|nd|rd) credit memo should be generated for order "#([^"]+)" and has total "([^"]+)"$/
     */
    public function stepDefinition(int $index, string $orderNumber, string $total): void
    {
        Assert::true($this->creditMemoIndexPage->hasCreditMemoWithData($index, $orderNumber, $total));
    }

    /**
     * @Then a pdf file should be successfully downloaded
     */
    public function pdfFileShouldBeSuccessfullyDownloaded(): void
    {
        Assert::true($this->pdfDownloadElement->isPdfFileDownloaded());
    }
}
