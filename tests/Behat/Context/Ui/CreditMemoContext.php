<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPageInterface;
use Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElementInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    private ShowPageInterface $orderShowPage;

    private CreditMemoIndexPageInterface $creditMemoIndexPage;

    private CreditMemoDetailsPageInterface $creditMemoDetailsPage;

    private PdfDownloadElementInterface $pdfDownloadElement;

    private ObjectRepository $creditMemoRepository;

    private CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider;

    public function __construct(
        ShowPageInterface $orderShowPage,
        CreditMemoIndexPageInterface $creditMemoIndexPage,
        CreditMemoDetailsPageInterface $creditMemoDetailsPage,
        PdfDownloadElementInterface $pdfDownloadElement,
        ObjectRepository $creditMemoRepository,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider
    ) {
        $this->orderShowPage = $orderShowPage;
        $this->creditMemoIndexPage = $creditMemoIndexPage;
        $this->creditMemoDetailsPage = $creditMemoDetailsPage;
        $this->pdfDownloadElement = $pdfDownloadElement;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->currentDateTimeImmutableProvider = $currentDateTimeImmutableProvider;
    }

    /**
     * @When I browse the details of the only credit memo generated for order :order
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(OrderInterface $order): void
    {
        $creditMemo = $this->creditMemoRepository->findBy(['order' => $order])[0];

        $this->creditMemoDetailsPage->open(['orderNumber' => $order->getNumber(), 'id' => $creditMemo->getId()]);
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
     * @When I filter credit memos by :channelName channel
     */
    public function filterCreditMemosByChannel(string $channelName): void
    {
        $this->creditMemoIndexPage->filterByChannel($channelName);
        $this->creditMemoIndexPage->filter();
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
     * @When I resend credit memo from order :orderNumber
     */
    public function resendCreditMemoToCustomer(string $orderNumber): void
    {
        $this->creditMemoIndexPage->resendCreditMemo($orderNumber);
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
     * @Then it should contain :quantity :productName product(s) with :netValue net value, :taxAmount tax amount and :grossValue gross value in :currencyCode currency
     */
    public function itShouldContainProductWithNetValueTaxAmountAndGrossValueInCurrency(
        int $quantity,
        string $productName,
        string $netValue,
        string $taxAmount,
        string $grossValue,
        string $currencyCode
    ): void {
        Assert::true(
            $this->creditMemoDetailsPage->hasItem($quantity, $productName, $netValue, $grossValue, $taxAmount, $currencyCode)
        );
    }

    /**
     * @Then it should contain :quantity :shipmentName shipment(s) with :netValue net value, :taxAmount tax amount and :grossValue gross value in :currencyCode currency
     */
    public function itShouldContainShipmentWithNetValueTaxAmountAndGrossValueInCurrency(
        int $quantity,
        string $shipmentName,
        string $netValue,
        string $taxAmount,
        string $grossValue,
        string $currencyCode
    ): void {
        Assert::true(
            $this->creditMemoDetailsPage->hasItem($quantity, $shipmentName, $netValue, $grossValue, $taxAmount, $currencyCode)
        );
    }

    /**
     * @Then it should contain a tax item :label with amount :amount in :currencyCode currency
     */
    public function itShouldContainATaxItemWithAmountInCurrency(string $label, string $amount, string $currencyCode): void
    {
        Assert::true($this->creditMemoDetailsPage->hasTaxItem($label, $amount, $currencyCode));
    }

    /**
     * @Then it should have sequential number generated from current date
     */
    public function shouldHaveSequentialNumberGeneratedFromCurrentDate(): void
    {
        Assert::same(
            $this->creditMemoDetailsPage->getNumber(),
            $this->currentDateTimeImmutableProvider->now()->format('Y/m').'/'.'000000001'
        );
    }

    /**
     * @Then it should be issued in :channelName channel
     */
    public function creditMemoShouldBeIssuedInChannel(string $channelName): void
    {
        Assert::same($this->creditMemoDetailsPage->getChannelName(), $channelName);
    }

    /**
     * @Then it should be issued from :customerName, :street, :postcode :city in the :country
     */
    public function itShouldBeIssuedFrom(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        Assert::same(
            $this->creditMemoDetailsPage->getFromAddress(),
            $customerName . ' ' . $street . ' ' . $city . ' ' . strtoupper($country->getName()) . ' ' . $postcode
        );
    }

    /**
     * @Then it should be issued to :company, :street, :postcode :city in the :country with :taxId tax ID
     */
    public function itShouldBeIssuedTo(
        string $company,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
        string $taxId
    ): void {
        Assert::same(
            $this->creditMemoDetailsPage->getToAddress(),
            $company . ' ' . $taxId . ' ' . $city . ' ' . $street . ' ' . strtoupper($country->getName()) . ' ' . $postcode
        );
    }

    /**
     * @Then its total should be :total in :currencyCode currency
     */
    public function itsTotalShouldBeInCurrency(string $total, string $currencyCode): void
    {
        Assert::same($this->creditMemoDetailsPage->getTotal(), $total);
        Assert::same($this->creditMemoDetailsPage->getTotalCurrencyCode(), $currencyCode);
    }

    /**
     * @Then its net total should be :total
     */
    public function itsNetTotalShouldBe(string $total): void
    {
        Assert::same($this->creditMemoDetailsPage->getNetTotal(), $total);
    }

    /**
     * @Then its tax total should be :total
     */
    public function itsTaxTotalShouldBe(string $total): void
    {
        Assert::same($this->creditMemoDetailsPage->getTaxTotal(), $total);
    }

    /**
     * @Then it should be commented with :comment
     */
    public function itShouldBeCommentedWith(string $comment): void
    {
        Assert::same($this->creditMemoDetailsPage->getComment(), $comment);
    }

    /**
     * @Then there should be :count credit memo(s) generated
     * @Then I should see :count credit memo(s)
     */
    public function thereShouldBeCreditMemosGenerated(int $count): void
    {
        Assert::same($this->creditMemoIndexPage->countItems(), $count);
    }

    /**
     * @Then /^(\d+)(?:st|nd|rd) credit memo should be generated for the (order "[^"]+"), have total "([^"]+)" and date of being issued$/
     */
    public function creditMemoShouldBeGeneratedForOrderHasTotalAndDateOfBeingIssued(
        int $index,
        OrderInterface $order,
        string $total
    ): void {
        $orderNumber = $order->getNumber();

        Assert::true(
            $this->creditMemoIndexPage->hasCreditMemoWithOrderNumber($index, $orderNumber),
            sprintf('Order number for %d credit memo should be %s', $index, $orderNumber)
        );

        Assert::true(
            $this->creditMemoIndexPage->hasCreditMemoWithTotal($index, $total),
            sprintf('Total for %d credit memo should be %s', $index, $total)
        );

        $creditMemos = $this->creditMemoRepository->findBy(['order' => $order], ['number' => 'ASC']);
        $issuedAt = $creditMemos[$index - 1]->getIssuedAt();

        Assert::true($this->creditMemoIndexPage->hasCreditMemoWithDateOfBeingIssued($index, $issuedAt),
            sprintf('Date of being issued for %d credit memo should be %s', $index, $issuedAt->format('Y-m-d H:i:s'))
        );
    }

    /**
     * @Then /^the only credit memo should be generated for order "#([^"]+)"$/
     */
    public function theOnlyCreditMemoShouldBeGeneratedForOrder(string $orderNumber): void
    {
        Assert::true($this->creditMemoIndexPage->hasSingleCreditMemoForOrder($orderNumber));
    }

    /**
     * @Then /^(\d+)(?:st|nd|rd) credit memo should be issued in "([^"]+)" channel$/
     */
    public function specificCreditMemoShouldBeIssuedInChannel(int $index, string $channelName): void
    {
        Assert::true($this->creditMemoIndexPage->hasCreditMemoWithChannel($index, $channelName));
    }

    /**
     * @Then a pdf file should be successfully downloaded
     */
    public function pdfFileShouldBeSuccessfullyDownloaded(): void
    {
        Assert::true($this->pdfDownloadElement->isPdfFileDownloaded());
    }

    /**
     * @Then /^I should see the credit memo with "([^"]+)" total as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldCreditMemoOrderByAscInTheList(string $creditMemoTotal, int $position): void
    {
        Assert::true($this->creditMemoDetailsPage->isCreditMemoInPosition($creditMemoTotal, $position));
    }

    /**
     * @Then the first credit memo should have order number :number
     */
    public function theFirstCreditMemoShouldHaveOrderNumber(string $orderNumber): void
    {
        Assert::eq($this->creditMemoIndexPage->getColumnFields('order')[0], $orderNumber);
    }

    /**
     * @When I switch the way credit memos are sorted by :fieldName
     * @When I sort credit memos descending by :fieldName
     */
    public function iSwitchSortingBy(string $fieldName): void
    {
        $this->creditMemoIndexPage->sortBy($fieldName);
    }
}
