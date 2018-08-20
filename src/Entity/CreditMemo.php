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

    /** @var string */
    private $localeCode;

    /** @var array */
    private $units;

    private $issuedAt;

    /** @var string */
    private $comment;

    public function __construct(
        string $number,
        string $orderNumber,
        int $total,
        string $currencyCode,
        string $localeCode,
        array $units,
        string $comment,
        \DateTimeInterface $issuedAt
    ) {
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
        $this->units = $units;
        $this->comment = $comment;
        $this->issuedAt = $issuedAt;
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

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getUnits(): array
    {
        $units = [];
        foreach ($this->units as $unit) {
            $units[] = CreditMemoUnit::unserialize($unit);
        }

        return $units;
    }

    public function getIssuedAt(): \DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
