<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;
use Webmozart\Assert\Assert;

final class ShipmentCreditMemoUnitGenerator implements CreditMemoUnitGeneratorInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function generate(int $unitId, int $amount = null): CreditMemoUnitInterface
    {
        /** @var AdjustmentInterface $shippingAdjustment */
        $shippingAdjustment = $this
            ->adjustmentRepository
            ->findOneBy(['id' => $unitId, 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
        ;
        Assert::notNull($shippingAdjustment);

        $creditMemoUnitTotal = $this->getCreditMemoUnitTotal($shippingAdjustment, $amount);

        return new CreditMemoUnit($shippingAdjustment->getLabel(), $creditMemoUnitTotal, 0);
    }

    private function getCreditMemoUnitTotal(AdjustmentInterface $shippingAdjustment, int $amount = null): int
    {
        Assert::lessThanEq($amount, $shippingAdjustment->getAmount());
        $creditMemoUnitTotal = null === $amount ? $shippingAdjustment->getAmount() : $amount;
        return $creditMemoUnitTotal;
    }
}
