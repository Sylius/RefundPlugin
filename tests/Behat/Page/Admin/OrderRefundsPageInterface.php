<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPageInterface;

interface OrderRefundsPageInterface extends SymfonyPageInterface
{
    public function countRefundableUnitsWithProduct(string $productName): int;

    public function getRefundedTotal(): string;

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void;

    public function pickAllUnitsToRefund(): void;

    public function pickOrderShipment(): void;

    public function choosePaymentMethod(string $paymentMethodName): void;

    public function comment(string $comment): void;

    public function refund(): void;

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool;

    public function isOrderShipmentAvailableToRefund(): bool;

    public function hasBackButton(): bool;

    public function canChoosePaymentMethod(): bool;

    public function isPaymentMethodVisible(string $paymentMethodName): bool;
}
