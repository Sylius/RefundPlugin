<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;

class CreditMemoFactory implements CreditMemoFactoryInterface
{
    /** @var string */
    protected $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

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
    ): CreditMemoInterface {
        return new $this->className(
            $id,
            $number,
            $orderNumber,
            $total,
            $currencyCode,
            $localeCode,
            $channel,
            $units,
            $comment,
            $issuedAt,
            $from,
            $to
        );
    }

    public function createNew()
    {
        throw new \RuntimeException();
    }
}
