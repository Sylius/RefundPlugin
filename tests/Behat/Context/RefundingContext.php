<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Tests\Sylius\RefundPlugin\Behat\Page\OrderRefundsPageInterface;
use Webmozart\Assert\Assert;

final class RefundingContext implements Context
{
    /** @var OrderRefundsPageInterface */
    private $orderRefundsPage;

    public function __construct(OrderRefundsPageInterface $orderRefundsPage)
    {
        $this->orderRefundsPage = $orderRefundsPage;
    }

    /**
     * @When I want to refund some units of order :orderNumber
     */
    public function wantToRefundSomeUnitsOfOrder(string $orderNumber): void
    {
        $this->orderRefundsPage->open(['orderNumber' => $orderNumber]);
    }

    /**
     * @Then I should be able to refund :count :productName products
     */
    public function iShouldBeAbleToRefundProducts(int $count, string $productName): void
    {
        Assert::same($count, $this->orderRefundsPage->countRefundableUnitsWithProduct($productName));
    }
}
