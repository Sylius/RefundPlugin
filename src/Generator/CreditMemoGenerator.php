<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CreditMemoUnit;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        NumberGenerator $creditMemoNumberGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
    }

    public function generate(string $orderNumber, int $total, array $unitIds, array $shipmentIds): CreditMemoInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $creditMemoUnits = [];

        foreach ($unitIds as $unitId) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($unitId);
            /** @var OrderItemInterface $orderItem */
            $orderItem = $orderItemUnit->getOrderItem();

            $discount = 0;
            $discount += $orderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
            $discount += $orderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
            $discount += $orderItemUnit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);

            $creditMemoUnit = new CreditMemoUnit(
                $orderItem->getProductName(),
                $orderItemUnit->getTotal(),
                $orderItemUnit->getTaxTotal(),
                $discount
            );

            $creditMemoUnits[] = $creditMemoUnit->serialize();
        }

        foreach ($shipmentIds as $shipmentId) {
            /** @var AdjustmentInterface $shipment */
            $shipment = $this->adjustmentRepository->findOneBy([
                'id' => $shipmentId,
                'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
            ]);

            $creditMemoUnits[] = new CreditMemoUnit($shipment->getLabel(), $shipment->getAmount(), 0, 0);
        }

        return new CreditMemo(
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $total,
            $order->getCurrencyCode(),
            $creditMemoUnits
        );
    }
}
