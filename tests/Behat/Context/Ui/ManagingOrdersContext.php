<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    /** @var ShowPageInterface */
    private $showPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        ShowPageInterface $showPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->showPage = $showPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I am viewing the summary of the order :order
     */
    public function viewingTheSummaryOfTheOrder(OrderInterface $order): void
    {
        $this->showPage->open(['id' => $order->getId()]);
    }

    /**
     * @Then I should be notified that the order should be paid
     */
    public function shouldBeNotifiedThatTheOrderShouldBePaid(): void
    {
        $this->notificationChecker->checkNotification(
            'Order should be paid for the units to could be refunded',
            NotificationType::failure()
        );
    }

    /**
     * @Then I should not be able to see refunds button
     */
    public function shouldNotBeAbleToSeeRefundsButton(): void
    {
        Assert::false($this->showPage->hasRefundsButton());
    }

    /**
     * @Then I should see :count refund payment(s) with status :status
     */
    public function shouldSeeRefundPaymentWithStatus(int $count, string $status): void
    {
        Assert::true($this->showPage->hasRefundPaymentsWithStatus($count, $status));
    }

    /**
     * @When I complete the first refund payment
     */
    public function completeTheFirstRefundPayment(): void
    {
        $this->showPage->completeRefundPayment(0);
    }

    /**
     * @Then I should be notified that refund payment has been successfully completed
     */
    public function shouldBeNotifiedThatRefundPaymentHasBeenSuccessfullyCompleted(): void
    {
        $this->notificationChecker->checkNotification(
            'Refund payment has been successfully completed',
            NotificationType::success()
        );
    }

    /**
     * @Then I should not be able to complete the first refund payment again
     */
    public function shouldNotBeAbleToCompleteTheFirstRefundPaymentAgain(): void
    {
        Assert::false($this->showPage->canCompleteRefundPayment(0));
    }
}
