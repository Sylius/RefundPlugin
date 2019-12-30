<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @final */
class CreditMemo implements CreditMemoInterface
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $number;

    /** @var OrderInterface */
    protected $order;

    /** @var int */
    protected $total;

    /** @var string */
    protected $currencyCode;

    /** @var string */
    protected $localeCode;

    /** @var ChannelInterface */
    protected $channel;

    /** @var array */
    protected $units;

    /** @var string */
    protected $comment;

    /** @var \DateTimeInterface */
    protected $issuedAt;

    /** @var CustomerBillingDataInterface */
    protected $from;

    /** @var ShopBillingDataInterface|null */
    protected $to;

    public function __construct(
        string $id,
        string $number,
        OrderInterface $order,
        int $total,
        string $currencyCode,
        string $localeCode,
        ChannelInterface $channel,
        array $units,
        string $comment,
        \DateTimeInterface $issuedAt,
        CustomerBillingDataInterface $from,
        ?ShopBillingDataInterface $to
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->order = $order;
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

    public function getOrder(): OrderInterface
    {
        return $this->order;
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

    public function getChannel(): ChannelInterface
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
