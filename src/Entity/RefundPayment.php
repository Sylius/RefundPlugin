<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\PaymentMethodInterface;

/** @final */
class RefundPayment implements RefundPaymentInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $amount;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $state;

    /** @var PaymentMethodInterface */
    private $paymentMethod;

    public function __construct(
        string $orderNumber,
        int $amount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod
    ) {
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->state = $state;
        $this->paymentMethod = $paymentMethod;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
