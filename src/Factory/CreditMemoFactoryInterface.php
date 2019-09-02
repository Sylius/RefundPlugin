<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;

interface CreditMemoFactoryInterface extends FactoryInterface
{
    public function createForData(
        string $id,
        string $number,
        string $orderNumber,
        int $total,
        string $currencyCode,
        string $localeCode,
        CreditMemoChannel $channel,
        array $units,
        string $comment,
        \DateTimeInterface $issuedAt,
        CustomerBillingDataInterface $from,
        ?ShopBillingData $to
    ): CreditMemoInterface;
}
