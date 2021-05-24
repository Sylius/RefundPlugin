<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Services\Factory;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;

final class FailedRefundPaymentFactory implements RefundPaymentFactoryInterface
{
    /** @var RefundPaymentFactoryInterface */
    private $baseRefundPaymentFactory;

    public function __construct(RefundPaymentFactoryInterface $baseRefundPaymentFactory)
    {
        $this->baseRefundPaymentFactory = $baseRefundPaymentFactory;
    }

    public function createWithData(
        string $orderNumber,
        int $amount,
        string $currencyCode,
        string $state,
        int $paymentMethodId
    ): RefundPaymentInterface {
        if (file_exists(__DIR__.'/refund-payment-failed.json')) {
            unlink(__DIR__.'/refund-payment-failed.json');

            throw new \Exception('Refund payment creation failed');
        }

        return $this->baseRefundPaymentFactory->createWithData($orderNumber, $amount, $currencyCode, $state, $paymentMethodId);
    }

    public function failRefundPaymentCreation(): void
    {
        touch(__DIR__.'/refund-payment-failed.json');
    }
}
