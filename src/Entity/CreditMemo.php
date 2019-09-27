<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemo implements CreditMemoInterface
{
    /** @var string */
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

    /** @var CreditMemoChannelInterface */
    private $channel;

    /** @var array */
    private $units;

    /** @var string */
    private $comment;

    /** @var \DateTimeInterface */
    private $issuedAt;

    /** @var CustomerBillingDataInterface */
    private $from;

    /** @var ShopBillingDataInterface|null */
    private $to;

    public function __construct(
        string $id,
        string $number,
        string $orderNumber,
        int $total,
        string $currencyCode,
        string $localeCode,
        CreditMemoChannelInterface $channel,
        array $units,
        string $comment,
        \DateTimeInterface $issuedAt,
        CustomerBillingDataInterface $from,
        ?ShopBillingDataInterface $to
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->total = $total;
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
        $this->channel = $channel;
        $this->units = $units;
        $this->comment = $comment;
        $this->issuedAt = $issuedAt;
        $this->from = $from;
        $this->to = $to;
    }

    public function getId(): string
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

    public function getChannel(): CreditMemoChannelInterface
    {
        return $this->channel;
    }

    public function getUnits(): array
    {
        $units = [];
        foreach ($this->units as $unit) {
            $units[] = CreditMemoUnit::unserialize($unit);
        }

        return $units;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getIssuedAt(): \DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function getFrom(): CustomerBillingDataInterface
    {
        return $this->from;
    }

    public function getTo(): ?ShopBillingDataInterface
    {
        return $this->to;
    }
}
