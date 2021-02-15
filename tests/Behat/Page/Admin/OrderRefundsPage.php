<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

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

    public function getUnitWithProductRefundedTotal(int $unitNumber, string $productName): string
    {
        $units = $this->getUnitsWithProduct($productName);

        return $units[$unitNumber]->find('css', '.unit-refunded-total')->getText();
    }

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void
    {
        $units = $this->getUnitsWithProduct($productName);

        $value = $units[$unitNumber]->find('css','td:nth-child(2)');

        $refunded = substr($this->getUnitRefundedTotal($value), 1);

        $total = substr($this->getUnitTotal($value), 1);

        /** @var double $total */
        $total = $total - $refunded;

        $units[$unitNumber]->find('css','td:nth-child(3) input')->setValue($total);
    }

    public function pickPartOfUnitWithProductToRefund(string $productName, int $unitNumber, string $amount): void
    {
        $units = $this->getUnitsWithProduct($productName);

        $units[$unitNumber]->find('css', '.partial-refund input')->setValue($amount);
    }

    public function pickAllUnitsToRefund(): void
    {
        $this->getDocument()->find('css', 'button[data-refund-all]')->click();
    }

    public function pickOrderShipment(): void
    {
        $orderShipment = $this->getOrderShipment();

        $total = $this->getUnitTotal($orderShipment);

        if ($this->getUnitRefundedTotal($orderShipment) !== null) {
            $refunded = $this->getUnitRefundedTotal($orderShipment);

            $total = substr($total, 1) - substr($refunded, 1);
        }

        $orderShipment->find('css','td:nth-child(3) input')->setValue($total);
    }

    public function pickPartOfOrderShipmentToRefund(string $amount): void
    {
        $orderShipment = $this->getOrderShipment();

        $orderShipment->find('css', '.partial-refund input')->setValue($amount);
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
        $this->getDocument()->find('css', '#page-button')->click();
    }

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool
    {
        return $this->isRefundable($this->getUnitsWithProduct($productName)[$unitNumber]);
    }

    public function eachRefundButtonIsDisabled(): bool
    {
        return
            $this->getDocument()->find('css', 'button[data-refund-clear]')->getAttribute('disabled') !== null &&
            $this->getDocument()->find('css', '#page-button')->getAttribute('disabled') !== null &&
            $this->getDocument()->find('css', 'button[data-refund-all]')->getAttribute('disabled') !== null
        ;
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

    public function isPaymentMethodSelected(string $paymentMethodName): bool
    {
        $paymentMethods = $this->getElement('payment_methods');

        if (null === $paymentMethods) {
            return false;
        }

        $optionField = $paymentMethods->find('xpath', "//option[@selected='selected']");
        if (null === $optionField) {
            return false;
        }

        if ($optionField->getText() != $paymentMethodName) {
            return false;
        }

        return true;
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

    private function getTextFromElement(?NodeElement $element): string
    {
        return ($element === null) ? '' : $element->getText();
    }

    private function isRefundable(NodeElement $element): bool
    {
        return $element->find('css', 'button')->getAttribute('disabled') === null;
    }

    private function getUnitRefundedTotal(NodeElement $orderShipment): string
    {
        $unitRefundedTotal = $orderShipment->find('css', '.unit-refunded-total');

        return $this->getTextFromElement($unitRefundedTotal);
    }

    private function getUnitTotal(NodeElement $orderShipment): string
    {
        $unitTotal = $orderShipment->find('css', '.unit-total');

        return $this->getTextFromElement($unitTotal);
    }
}
