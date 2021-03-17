<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    /** @var CreditMemoInterface */
    private $creditMemo;

    /** @var ObjectRepository */
    private $creditMemoRepository;

    /** @var CurrentDateTimeImmutableProviderInterface */
    private $currentDateTimeImmutableProvider;

    public function __construct(
        ObjectRepository $creditMemoRepository,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->currentDateTimeImmutableProvider = $currentDateTimeImmutableProvider;
    }

    /**
     * @When I browse the details of the only credit memo generated for order :order
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(OrderInterface $order): void
    {
        $creditMemos = $this->creditMemoRepository->findBy(['order' => $order]);

        $this->creditMemo = $creditMemos[0];
    }

    /**
     * @Then I should have :count credit memo generated for order :order
     */
    public function shouldHaveCountCreditMemoGeneratedForThisOrder(int $count, OrderInterface $order): void
    {
        $creditMemos = $this->creditMemoRepository->findBy(['order' => $order]);

        Assert::count($creditMemos, $count);
    }

    /**
     * @Then it should have sequential number generated from current date
     */
    public function shouldHaveSequentialNumberGeneratedFromCurrentDate(): void
    {
        Assert::same(
            $this->creditMemo->getNumber(),
            $this->currentDateTimeImmutableProvider->now()->format('Y/m').'/'.'000000001'
        );
    }

    /**
     * @Then /^it should contain (\d+) "([^"]+)" product(?:|s) with ("[^"]+") net value, ("[^"]+") tax amount and ("[^"]+") gross value in "([^"]+)" currency$/
     * @Then /^it should contain (\d+) "([^"]+)" shipment(?:|s) with ("[^"]+") net value, ("[^"]+") tax amount and ("[^"]+") gross value in "([^"]+)" currency$/
     */
    public function itShouldContainProductWithNetValueTaxAmountAndGrossValueInCurrency(
        int $quantity,
        string $productName,
        int $netValue,
        int $taxAmount,
        int $grossValue,
        string $currencyCode
    ): void {
        $lineItems = $this->creditMemo->getLineItems();

        foreach ($lineItems as $item) {
            if (
                $item->name() === $productName &&
                $item->quantity() === $quantity &&
                $item->netValue() === $netValue &&
                $item->taxAmount() === $taxAmount &&
                $item->grossValue() === $grossValue
            ) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no item with given data.');
    }

    /**
     * @Then /^it should contain a tax item "([^"]+)" with amount ("[^"]+") in "([^"]+)" currency$/
     */
    public function itShouldContainATaxItemWithAmountInCurrency(string $label, int $amount, string $currencyCode): void
    {
        /** @var TaxItemInterface $taxItem */
        foreach ($this->creditMemo->getTaxItems() as $item) {
            if ($item->label() === $label && $item->amount() === $amount) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('There is no tax item %s with given amount.', $label));
    }

    /**
     * @Then it should be issued in :channelName channel
     */
    public function creditMemoShouldBeIssuedInChannel(string $channelName): void
    {
        Assert::same($this->creditMemo->getChannel()->getName(), $channelName);
    }

    /**
     * @Then /^its total should be ("[^"]+") in "([^"]+)" currency$/
     */
    public function creditMemoTotalShouldBe(int $total, string $currencyCode): void
    {
        Assert::same($this->creditMemo->getTotal(), $total);
        Assert::same($this->creditMemo->getCurrencyCode(), $currencyCode);
    }

    /**
     * @Then /^its net total should be ("[^"]+")$/
     */
    public function itsNetTotalShouldBe(int $total): void
    {
        Assert::same($this->creditMemo->getNetValueTotal(), $total);
    }

    /**
     * @Then /^its tax total should be ("[^"]+")$/
     */
    public function itsTaxTotalShouldBe(int $total): void
    {
        Assert::same($this->creditMemo->getTaxTotal(), $total);
    }

    /**
     * @Then it should be commented with :comment
     */
    public function itShouldBeCommentedWith(string $comment): void
    {
        Assert::same($this->creditMemo->getComment(), $comment);
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
        $customerBillingData = $this->creditMemo->getFrom();

        Assert::same($customerBillingData->fullName(), $customerName);
        Assert::same($customerBillingData->street(), $street);
        Assert::same($customerBillingData->postcode(), $postcode);
        Assert::same($customerBillingData->city(), $city);
        Assert::same($customerBillingData->countryCode(), $country->getCode());
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
        $shopBillingData = $this->creditMemo->getTo();

        Assert::same($company, $shopBillingData->company());
        Assert::same($street, $shopBillingData->street());
        Assert::same($postcode, $shopBillingData->postcode());
        Assert::same($city, $shopBillingData->city());
        Assert::same($country->getCode(), $shopBillingData->countryCode());
        Assert::same($taxId, $shopBillingData->taxId());
    }
}
