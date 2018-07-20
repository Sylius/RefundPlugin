<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Webmozart\Assert\Assert;

final class CreditMemoContext implements Context
{
    /** @var CreditMemoInterface */
    private $creditMemo;

    /** @var ObjectRepository */
    private $creditMemoRepository;

    public function __construct(ObjectManager $creditMemoManager)
    {
        $this->creditMemoRepository = $creditMemoManager->getRepository(CreditMemo::class);
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
     * @Then this credit memo should contain :count :productName product, with :discount discount and :tax tax applied
     */
    public function thisCreditMemoShouldContainProductWithDiscountAndTaxApplied(
        int $count,
        string $productName,
        int $discount,
        int $tax
    ): void {
        // check data of saved credit memo

        throw new PendingException();
    }

    /**
     * @Then /^its total should be ("[^"]+")$/
     */
    public function creditMemoTotalShouldBe(int $total): void
    {
        Assert::same($this->creditMemo->getTotal(), $total);
    }
}
