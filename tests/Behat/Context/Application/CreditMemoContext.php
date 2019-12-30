<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    /** @var CreditMemoInterface */
    private $creditMemo;

    /** @var ObjectRepository */
    private $creditMemoRepository;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    public function __construct(ObjectRepository $creditMemoRepository, CurrentDateTimeProviderInterface $currentDateTimeProvider)
    {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
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
            $this->currentDateTimeProvider->now()->format('Y/m').'/'.'000000001'
        );
    }

    /**
     * @Then /^this credit memo should contain (\d+) "([^"]+)" product with ("[^"]+") tax applied$/
     */
    public function thisCreditMemoShouldContainProductWithTaxApplied(
        int $count,
        string $productName,
        int $taxesTotal
    ): void {
        $units = $this->creditMemo->getUnits();

        Assert::same(count($units), $count);
        Assert::same($units[0]->getProductName(), $productName);
        Assert::same($units[0]->getTaxesTotal(), $taxesTotal);
    }

    /**
     * @Then /^this credit memo should contain (\d+) "([^"]+)" shipment with ("[^"]+") total$/
     */
    public function thisCreditMemoShouldContainShipmentWithTotal(
        int $count,
        string $shipmentName,
        int $total
    ): void {
        $units = $this->creditMemo->getUnits();

        Assert::same(count($units), $count);
        Assert::same($units[0]->getProductName(), $shipmentName);
        Assert::same($units[0]->getTotal(), $total);
    }

    /**
     * @Then it should be issued in :channelName channel
     */
    public function creditMemoShouldBeIssuedInChannel(string $channelName): void
    {
        Assert::same($this->creditMemo->getChannel()->getName(), $channelName);
    }

    /**
     * @Then /^its total should be ("[^"]+")$/
     */
    public function creditMemoTotalShouldBe(int $total): void
    {
        Assert::same($this->creditMemo->getTotal(), $total);
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

        Assert::same($customerBillingData->customerName(), $customerName);
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
