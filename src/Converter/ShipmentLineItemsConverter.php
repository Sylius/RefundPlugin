<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Webmozart\Assert\Assert;

final class ShipmentLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function convert(array $units): array
    {
        Assert::allIsInstanceOf($units, ShipmentRefund::class);

        $lineItems = [];

        /** @var ShipmentRefund $shipmentRefund */
        foreach ($units as $shipmentRefund) {
            $lineItems[] = $this->convertUnitRefundToLineItem($shipmentRefund);
        }

        return $lineItems;
    }

    private function convertUnitRefundToLineItem(ShipmentRefund $shipmentRefund): LineItemInterface
    {
        /** @var AdjustmentInterface|null $shippingAdjustment */
        $shippingAdjustment = $this
            ->adjustmentRepository
            ->findOneBy(['id' => $shipmentRefund->id(), 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
        ;
        Assert::notNull($shippingAdjustment);
        Assert::lessThanEq($shipmentRefund->total(), $shippingAdjustment->getAmount());

        return new LineItem(
            $shippingAdjustment->getLabel(),
            1,
            $shipmentRefund->total(),
            $shipmentRefund->total(),
            $shipmentRefund->total(),
            $shipmentRefund->total(),
            0
        );
    }
}
