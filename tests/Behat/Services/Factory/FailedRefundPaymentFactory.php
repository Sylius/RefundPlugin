<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Services\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;

final class FailedRefundPaymentFactory implements RefundPaymentFactoryInterface
{
    public const FAILED_FILE = __DIR__.'/refund-payment-failed.json';

    private RefundPaymentFactoryInterface $baseRefundPaymentFactory;

    public function __construct(RefundPaymentFactoryInterface $baseRefundPaymentFactory)
    {
        $this->baseRefundPaymentFactory = $baseRefundPaymentFactory;
    }

    public function createNew(): RefundPaymentInterface
    {
        throw new \InvalidArgumentException('Default creation method is forbidden for this object. Use `createWithData` instead.');
    }

    public function createWithData(
        OrderInterface $order,
        int $amount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod
    ): RefundPaymentInterface {
        if (file_exists(self::FAILED_FILE)) {
            unlink(self::FAILED_FILE);

            throw new \Exception('Refund payment creation failed');
        }

        return $this->baseRefundPaymentFactory->createWithData($order, $amount, $currencyCode, $state, $paymentMethod);
    }

    public function failRefundPaymentCreation(): void
    {
        touch(self::FAILED_FILE);
    }
}
