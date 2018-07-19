<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

final class CreditMemoContext implements Context
{
    /** @var CreditMemo */
    private $creditMemo;

    /**
     * @When I browse the details of the only credit memo generated for order :orderNumber
     */
    public function browseTheDetailsOfTheOnlyCreditMemoGeneratedForOrder(string $orderNumber): void
    {
        // get credit memo for order from repository and save in memory

        throw new PendingException();
    }

    /**
     * @Then I should have :count credit memo generated
     */
    public function shouldHaveCountCreditMemoGenerated(int $count): void
    {
        // get all credit memos for order from repository and check if there is one

        throw new PendingException();
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
     * @Then its total should be :total
     */
    public function creditMemoTotalShouldBe(string $total): void
    {
        // check credit memo total

        throw new PendingException();
    }
}
