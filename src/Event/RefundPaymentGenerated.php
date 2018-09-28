<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class RefundPaymentGenerated
{
    /** @var int */
    private $id;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $amount;

    /** @var string */
    private $currencyCode;

    /** @var int */
    private $paymentMethodId;

    public function __construct(int $id, string $orderNumber, int $amount, string $currencyCode, int $paymentMethodId)
    {
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->paymentMethodId = $paymentMethodId;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function paymentMethodId(): int
    {
        return $this->paymentMethodId;
    }
}
