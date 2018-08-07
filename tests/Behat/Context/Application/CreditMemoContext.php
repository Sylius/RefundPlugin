<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
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
     * @When I browse the details of the only credit memo generated for order :orderNumber
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(string $orderNumber): void
    {
        $creditMemos = $this->creditMemoRepository->findBy(['orderNumber' => $orderNumber]);

        $this->creditMemo = $creditMemos[0];
    }

    /**
     * @Then I should have :count credit memo generated for order :orderNumber
     */
    public function shouldHaveCountCreditMemoGeneratedForThisOrder(int $count, string $orderNumber): void
    {
        $creditMemos = $this->creditMemoRepository->findBy(['orderNumber' => $orderNumber]);

        Assert::count($creditMemos, 1);
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
     * @Then /^its total should be ("[^"]+")$/
     */
    public function creditMemoTotalShouldBe(int $total): void
    {
        Assert::same($this->creditMemo->getTotal(), $total);
    }
}
