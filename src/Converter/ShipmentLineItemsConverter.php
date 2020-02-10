<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class ShipmentLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    public function __construct(
        RepositoryInterface $adjustmentRepository,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function convert(array $units): array
    {
        $lineItems = [];

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            $lineItems[] = $this->convertUnitRefundToLineItem($unitRefund);
        }

        return $lineItems;
    }

    private function convertUnitRefundToLineItem(UnitRefundInterface $unitRefund): LineItemInterface
    {
        /** @var AdjustmentInterface|null $shippingAdjustment */
        $shippingAdjustment = $this
            ->adjustmentRepository
            ->findOneBy(['id' => $unitRefund->id(), 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
        ;
        Assert::notNull($shippingAdjustment);
        Assert::lessThanEq($unitRefund->total(), $shippingAdjustment->getAmount());

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find(['id' => $unitRefund->id()]);
        Assert::notNull($shipment);
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $shipment->getUnits()->first()->getAdjustments()->first();
        $adjustment->getAmount();

        return new LineItem(
            $shippingAdjustment->getLabel(),
            1,
            $unitRefund->total(),
            $unitRefund->total(),
            $unitRefund->total(),
            $unitRefund->total(),
            $adjustment->getAmount()
        );
    }
}
