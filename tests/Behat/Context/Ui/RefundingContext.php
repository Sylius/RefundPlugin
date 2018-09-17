<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\UnexpectedPageException;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\OrderRefundsPageInterface;
use Webmozart\Assert\Assert;

final class RefundingContext implements Context
{
    /** @var OrderRefundsPageInterface */
    private $orderRefundsPage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        OrderRefundsPageInterface $orderRefundsPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->orderRefundsPage = $orderRefundsPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to refund some units of order :orderNumber
     */
    public function wantToRefundSomeUnitsOfOrder(string $orderNumber): void
    {
        $this->orderRefundsPage->open(['orderNumber' => $orderNumber]);
    }

    /**
     * @When I try to refund some units of order :orderNumber
     */
    public function tryToRefundSomeUnitsOfOrder(string $orderNumber): void
    {
        try {
            $this->orderRefundsPage->open(['orderNumber' => $orderNumber]);
        } catch (UnexpectedPageException $exception) {
        }
    }

    /**
     * @When /^I decide to refund (\d)st "([^"]+)" product with "([^"]+)" payment$/
     * @When /^I decide to refund (\d)st "([^"]+)" product with ("[^"]+" payment) and "([^"]+)" comment$/
     */
    public function decideToRefundProduct(
        int $unitNumber,
        string $productName,
        string $paymentMethod,
        string $comment = ''
    ): void {
        $this->orderRefundsPage->pickUnitWithProductToRefund($productName, $unitNumber-1);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->comment($comment);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decide to refund (\d)st "([^"]+)" product with ("[^"]+" payment) and very long comment$/
     */
    public function decideToRefundProductWithVeryLongComment(
        int $unitNumber,
        string $productName,
        string $paymentMethod
    ): void {
        $this->orderRefundsPage->pickUnitWithProductToRefund($productName, $unitNumber-1);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->comment($this->provideLongComment());
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decide to refund (\d)st "([^"]+)" and (\d)st "([^"]+)" products with "([^"]+)" payment$/
     */
    public function decideToRefundMultipleProduct(
        int $firstUnitNumber,
        string $firstProductName,
        int $secondUnitNumber,
        string $secondProductName,
        string $paymentMethod,
        string $comment = ''
    ): void {
        $this->orderRefundsPage->pickUnitWithProductToRefund($firstProductName, $firstUnitNumber-1);
        $this->orderRefundsPage->pickUnitWithProductToRefund($secondProductName, $secondUnitNumber-1);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->comment($comment);
        $this->orderRefundsPage->refund();
    }

    /**
     * @Given /^I decide to refund "\$([^"]+)" from (\d)st "([^"]+)" product with "([^"]+)" payment$/
     */
    public function decideToRefundPartFromProductWithPayment(
        string $partialPrice,
        int $unitNumber,
        string $productName,
        string $paymentMethod
    ): void {
        $this->orderRefundsPage->pickPartOfUnitWithProductToRefund($productName, $unitNumber-1, $partialPrice);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decided to refund (\d)st "([^"]+)" product of the order "([^"]+)" with "([^"]+)" payment$/
     */
    public function decidedToRefundProduct(
        int $unitNumber,
        string $productName,
        string $orderNumber,
        string $paymentMethod
    ): void {
        $this->orderRefundsPage->open(['orderNumber' => $orderNumber]);
        $this->orderRefundsPage->pickUnitWithProductToRefund($productName, $unitNumber-1);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When I decide to refund all units of this order with :paymentMethod payment
     */
    public function decideToRefundAllUnits(string $paymentMethod): void
    {
        $this->orderRefundsPage->pickAllUnitsToRefund();
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decide to refund order shipment with "([^"]+)" payment$/
     */
    public function decideToRefundOrderShipment(string $paymentMethod): void
    {
        $this->orderRefundsPage->pickOrderShipment();
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decide to refund "\$([^"]+)" from order shipment with "([^"]+)" payment$/
     * @When /^I try to refund "\$([^"]+)" from order shipment with "([^"]+)" payment$/
     */
    public function decideToRefundPartOfOrderShipment(string $amount, string $paymentMethod): void
    {
        $this->orderRefundsPage->pickPartOfOrderShipmentToRefund($amount);
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When /^I decide to refund order shipment and (\d)st "([^"]+)" product with "([^"]+)" payment$/
     */
    public function decideToRefundProductAndShipment(int $unitNumber, string $productName, string $paymentMethod): void
    {
        $this->orderRefundsPage->pickUnitWithProductToRefund($productName, $unitNumber-1);
        $this->orderRefundsPage->pickOrderShipment();
        $this->orderRefundsPage->choosePaymentMethod($paymentMethod);
        $this->orderRefundsPage->refund();
    }

    /**
     * @When I refund zero items
     */
    public function refundZeroItems(): void
    {
        $this->orderRefundsPage->refund();
    }

    /**
     * @Then I should be able to refund :count :productName products
     */
    public function shouldBeAbleToRefundProducts(int $count, string $productName): void
    {
        Assert::same($count, $this->orderRefundsPage->countRefundableUnitsWithProduct($productName));
    }

    /**
     * @Then I should be able to go back to order details
     */
    public function shouldBeAbleToGoBackToOrderDetails(): void
    {
        Assert::true($this->orderRefundsPage->hasBackButton());
    }

    /**
     * @Then I should be notified that selected order units have been successfully refunded
     */
    public function shouldBeNotifiedThatSelectedOrderUnitsHaveBeenSuccessfullyRefunded(): void
    {
        $this->notificationChecker->checkNotification(
            'Selected order units have been successfully refunded',
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that I cannot refund more money than the order unit total
     * @Then I should be notified that I cannot refund more money than the shipment total
     */
    public function shouldBeNotifiedThatICannotRefundMoreMoneyThanTheRefundedUnitTotal(): void
    {
        $this->notificationChecker->checkNotification(
            'You cannot refund more money than the refunded unit total',
            NotificationType::failure()
        );
    }

    /**
     * @Then I should be notified that at least one unit should be selected to refund
     */
    public function shouldBeNotifiedThatAtLeastOneUnitShouldBeSelectedToRefund(): void
    {
        $this->notificationChecker->checkNotification(
            'At least one unit should be selected to refund',
            NotificationType::failure()
        );
    }

    /**
     * @Then I should be notified that credit memo comment is too long
     */
    public function shouldBeNotifiedThatCreditMemoCommentIsTooLong(): void
    {
        $this->notificationChecker->checkNotification(
            'Credit memo comment is too long',
            NotificationType::failure()
        );
    }

    /**
     * @Then this order refunded total should (still) be :refundedTotal
     */
    public function refundedTotalShouldBe(string $refundedTotal): void
    {
        Assert::same($this->orderRefundsPage->getRefundedTotal(), $refundedTotal);
    }

    /**
     * @Then /^(\d+)st "([^"]+)" product should have "([^"]+)" refunded$/
     */
    public function productShouldHaveSomeAmountRefunded(int $unitNumber, string $productName, string $amount): void
    {
        Assert::same($this->orderRefundsPage->getUnitWithProductRefundedTotal($unitNumber-1, $productName), $amount);
    }

    /**
     * @Then /^I should not be able to refund (\d)(?:|st|nd|rd) unit with product "([^"]+)"$/
     */
    public function shouldNotBeAbleToRefundUnitWithProduct(int $unitNumber, string $productName): void
    {
        Assert::false($this->orderRefundsPage->isUnitWithProductAvailableToRefund($productName, $unitNumber-1));
    }

    /**
     * @Then I should still be able to refund order shipment with :paymentMethodName payment
     */
    public function shouldStillBeAbleToRefundOrderShipment(): void
    {
        Assert::true($this->orderRefundsPage->isOrderShipmentAvailableToRefund());
    }

    /**
     * @Then I should not be able to refund order shipment
     */
    public function shouldNotBeAbleToRefundOrderShipment(): void
    {
        Assert::false($this->orderRefundsPage->isOrderShipmentAvailableToRefund());
    }

    /**
     * @Then /^I should(?:| still) be able to refund (\d)(?:|st|nd|rd) unit with product "([^"]+)" with ("[^"]+" payment)$/
     */
    public function shouldBeAbleToRefundUnitWithProduct(int $unitNumber, string $productName): void
    {
        Assert::true($this->orderRefundsPage->isUnitWithProductAvailableToRefund($productName, $unitNumber-1));
    }

    /**
     * @Then I should be able to choose refund payment method
     */
    public function shouldBeAbleToChooseRefundPaymentMethod(): void
    {
        Assert::true($this->orderRefundsPage->canChoosePaymentMethod());
    }

    /**
     * @Then there should be :payment payment method
     */
    public function thereShouldBePaymentMethod(string $payment): void
    {
        Assert::true($this->orderRefundsPage->isPaymentMethodVisible($payment));
    }

    private function provideLongComment(): string
    {
        return 'Tu ne quaesieris scire nefas, quem mihi quem tibi finem di dederint, Leuconoe, nec Babylonios temptaris numeros. Ut melius quidquid erit pati. Seu plures hiemes sue tribuit Iuppiter ultimam. Qae nunc oppositis debilitat pumicibus mare Tyrrenum: sapias vina liques et spatio brevi. Spem longam resecens. Dum loquimur fugerit invida Aetas: CARPE DIEM, quam minimum credula postero.';
    }
}
