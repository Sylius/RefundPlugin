<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface CreditMemoInterface extends ResourceInterface
{
    public function getNumber(): string;

    public function getOrderNumber(): string;

    public function getTotal(): int;

    public function getCurrencyCode(): string;

    public function getLocaleCode(): string;

    public function getChannel(): ChannelInterface;

    public function getUnits(): array;

    public function getComment(): string;

    public function getIssuedAt(): \DateTimeInterface;

    public function getFrom(): CustomerBillingDataInterface;

    public function getTo(): ?ShopBillingDataInterface;
}
