<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    private ShowPageInterface $showPage;

    private IndexPageInterface $indexPage;

    private NotificationCheckerInterface $notificationChecker;

    public function __construct(
        ShowPageInterface $showPage,
        IndexPageInterface $indexPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->showPage = $showPage;
        $this->indexPage = $indexPage;
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
     * @Then I should not see refunds button
     */
    public function iShouldNotSeeRefundsButton(): void
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
     * @Then I should not see any refund payments
     */
    public function shouldNotSeeAnyRefundPayments(): void
    {
        Assert::same($this->showPage->countRefundPayments(), 0);
    }

    /**
     * @Then I should not see any credit memos
     */
    public function shouldNotSeeAnyCreditMemos(): void
    {
        Assert::same($this->showPage->countCreditMemos(), 0);
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

    /**
     * @Then /^(this order)'s payment state should be "([^"]+)"$/
     */
    public function thisOrderSPaymentStateShouldBe(OrderInterface $order, string $orderPaymentState): void
    {
        $this->indexPage->open();
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'number' => $order->getNumber(),
            'paymentState' => $orderPaymentState,
        ]));
    }

    /**
     * @Then I should be redirected to the order :order show page
     */
    public function iShouldBeRedirectedToTheOrderShowPage(OrderInterface $order): void
    {
        Assert::true($this->showPage->isOpen(['id' => $order->getId()]));
    }

    /**
     * @Then I should be notified that I cannot refund a free order
     */
    public function iShouldBeNotifiedThatICannotRefundAFreeOrder(): void
    {
        $this->notificationChecker->checkNotification(
            'You cannot refund a free order',
            NotificationType::failure()
        );
    }
}
