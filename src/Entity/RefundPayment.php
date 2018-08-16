<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class RefundPayment implements RefundPaymentInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $number;

    /** @var int */
    private $amount;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $state;

    public function __construct(
        string $number,
        int $amount,
        string $currencyCode,
        string $state
    ) {
        $this->number = $number;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->state = $state;
    }

    public function getNumber(): string
    {
        return $this->number;
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
}
