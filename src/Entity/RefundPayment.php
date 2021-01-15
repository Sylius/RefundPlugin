<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\PaymentMethodInterface;

class RefundPayment implements RefundPaymentInterface
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $orderNumber;

    /** @var int */
    protected $amount;

    /** @var string */
    protected $currencyCode;

    /** @var string */
    protected $state;

    /** @var PaymentMethodInterface */
    protected $paymentMethod;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
