<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

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

    /** @var Collection|LineItemInterface[] */
    protected $lineItems;

    /** @var Collection|TaxItemInterface[] */
    protected $taxItems;

    /** @var string */
    protected $comment;

    /** @var \DateTimeImmutable */
    protected $issuedAt;

    /** @var CustomerBillingDataInterface */
    protected $from;

    /** @var ShopBillingDataInterface|null */
    protected $to;

    public function __construct()
    {
        $this->lineItems = new ArrayCollection();
        $this->taxItems = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    public function setLineItems(Collection $lineItems): void
    {
        $this->lineItems = $lineItems;
    }

    public function getTaxItems(): Collection
    {
        return $this->taxItems;
    }

    public function setTaxItems(Collection $taxItems): void
    {
        $this->taxItems = $taxItems;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getIssuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    public function getFrom(): CustomerBillingDataInterface
    {
        return $this->from;
    }

    public function setFrom(CustomerBillingDataInterface $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?ShopBillingDataInterface
    {
        return $this->to;
    }

    public function setTo(?ShopBillingDataInterface $to): void
    {
        $this->to = $to;
    }

    public function getNetValueTotal(): int
    {
        $sum = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->getLineItems() as $lineItem) {
            $sum += $lineItem->netValue();
        }

        return $sum;
    }

    public function getTaxTotal(): int
    {
        $sum = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->getLineItems() as $lineItem) {
            $sum += $lineItem->taxAmount();
        }

        return $sum;
    }
}
