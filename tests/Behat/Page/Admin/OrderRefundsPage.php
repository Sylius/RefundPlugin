<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;

final class OrderRefundsPage extends SymfonyPage implements OrderRefundsPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_refund_order_refunds_list';
    }

    public function countRefundableUnitsWithProduct(string $productName): int
    {
        return count($this->getUnitsWithProduct($productName));
    }

    public function getRefundedTotal(): string
    {
        return str_replace('Refunded total: ', '', $this->getElement('refunded_total')->getText());
    }

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void
    {
        $units = $this->getUnitsWithProduct($productName);

        $units[$unitNumber]->find('css', '.checkbox input')->check();
    }

    public function pickAllUnitsToRefund(): void
    {
        $this->getDocument()->find('css', '#refund-all')->click();
    }

    public function pickOrderShipment(): void
    {
        $orderShipment = $this->getOrderShipment();

        $orderShipment->find('css', '.checkbox input')->check();
    }

    public function choosePaymentMethod(string $paymentMethodName): void
    {
        $paymentMethods = $this->getElement('payment_methods');

        $paymentMethods->selectOption($paymentMethodName);
    }

    public function comment(string $comment): void
    {
        $this->getDocument()->fillField('Comment', $comment);
    }

    public function refund(): void
    {
        $this->getDocument()->pressButton('Refund');
    }

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool
    {
        return $this->isRefundable($this->getUnitsWithProduct($productName)[$unitNumber]);
    }

    public function isOrderShipmentAvailableToRefund(): bool
    {
        return $this->isRefundable($this->getOrderShipment());
    }

    public function hasBackButton(): bool
    {
        return null !== $this->getDocument()->find('css', 'a:contains("Back")');
    }

    public function canChoosePaymentMethod(): bool
    {
        return null !== $this->getElement('payment_methods');
    }

    public function isPaymentMethodVisible(string $paymentMethodName): bool
    {
        $paymentMethods = $this->getElement('payment_methods');

        return strpos($paymentMethods->getText(), $paymentMethodName) !== false;
    }

    protected function getDefinedElements(): array
    {
        return [
            'payment_methods' => '#payment-methods',
            'refunded_total' => '#refunded-total',
        ];
    }

    /** @return array|NodeElement[] */
    private function getUnitsWithProduct(string $productName): array
    {
        return $this->getDocument()->findAll('css', sprintf('#refunds .unit:contains("%s")', $productName));
    }

    private function getOrderShipment(): NodeElement
    {
        return $this->getDocument()->find('css', '#refunds .shipment');
    }

    private function isRefundable(NodeElement $element): bool
    {
        return !$element->find('css', '.checkbox')->hasClass('disabled');
    }
}
