<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Webmozart\Assert\Assert;

final class RemainingTotalProvider implements RemainingTotalProviderInterface
{
    private RepositoryInterface $orderItemUnitRepository;

    private RepositoryInterface $adjustmentRepository;

    private RepositoryInterface $refundRepository;

    public function __construct(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
    ) {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->refundRepository = $refundRepository;
    }

    public function getTotalLeftToRefund(int $id, RefundTypeInterface $type): int
    {
        $unitTotal = $this->getRefundUnitTotal($id, $type);
        $refunds = $this->refundRepository->findBy(['refundedUnitId' => $id, 'type' => $type]);

        if (count($refunds) === 0) {
            return $unitTotal;
        }

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $unitTotal - $refundedTotal;
    }

    private function getRefundUnitTotal(int $id, RefundTypeInterface $refundType): int
    {
        if ($refundType->getValue() === RefundTypeInterface::ORDER_ITEM_UNIT) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($id);
            Assert::notNull($orderItemUnit);

            return $orderItemUnit->getTotal();
        }

        /** @var AdjustmentInterface $shippingAdjustment */
        $shippingAdjustment = $this->adjustmentRepository->findOneBy([
            'id' => $id,
            'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
        ]);
        Assert::notNull($shippingAdjustment);

        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        return $shipment->getAdjustmentsTotal();
    }
}
