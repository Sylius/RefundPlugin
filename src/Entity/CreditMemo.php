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

    /** @var array */
    private $units;

    public function __construct(string $number, string $orderNumber, int $total, string $currencyCode, array $units)
    {
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->currencyCode = $currencyCode;
        $this->units = $units;
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

    public function getUnits(): array
    {
        $units = [];
        foreach ($this->units as $unit) {
            $units[] = CreditMemoUnit::unserialize($unit);
        }

        return $units;
    }
}
