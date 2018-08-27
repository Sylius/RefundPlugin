<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface
    {
        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($unitId);
        Assert::notNull($orderItemUnit);
        Assert::lessThanEq($amount, $orderItemUnit->getTotal());

        /** @var OrderItemInterface $orderItem */
        $orderItem = $orderItemUnit->getOrderItem();
        $total = $orderItemUnit->getTotal();

        if ($amount !== null && $amount === $total) {
            return new CreditMemoUnit(
                $orderItem->getProductName(),
                $total,
                $orderItemUnit->getTaxTotal()
            );
        }

        $taxTotal = (int) ($orderItemUnit->getTaxTotal() * ($amount / $total));

        return new CreditMemoUnit(
            $orderItem->getProductName(),
            $amount,
            $taxTotal
        );
    }
}
