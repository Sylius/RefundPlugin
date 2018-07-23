<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemo implements CreditMemoInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $number;

    /** @var string */
    private $orderNumber;

    /** @var int */
    private $total;

    /** @var string */
    private $currencyCode;

    public function __construct(string $number, string $orderNumber, int $total, string $currencyCode)
    {
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->currencyCode = $currencyCode;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
