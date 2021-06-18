<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface OrderRefundsPageInterface extends SymfonyPageInterface
{
    public function countRefundableUnitsWithProduct(string $productName): int;

    public function getRefundedTotal(): string;

    public function getUnitWithProductRefundedTotal(int $unitNumber, string $productName): string;

    public function pickUnitWithProductToRefund(string $productName, int $unitNumber): void;

    public function pickPartOfUnitWithProductToRefund(string $productName, int $unitNumber, string $amount): void;

    public function pickAllUnitsToRefund(): void;

    public function pickOrderShipment(?string $shippingMethodName = null): void;

    public function pickPartOfOrderShipmentToRefund(string $amount): void;

    public function choosePaymentMethod(string $paymentMethodName): void;

    public function comment(string $comment): void;

    public function refund(): void;

    public function isUnitWithProductAvailableToRefund(string $productName, int $unitNumber): bool;

    public function eachRefundButtonIsDisabled(): bool;

    public function isOrderShipmentAvailableToRefund(): bool;

    public function hasBackButton(): bool;

    public function canChoosePaymentMethod(): bool;

    public function isPaymentMethodVisible(string $paymentMethodName): bool;

    public function getOriginalPaymentMethodName(): string;
}
