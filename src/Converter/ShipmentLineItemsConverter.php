<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;
use Webmozart\Assert\Assert;

final class ShipmentLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var TaxRateProviderInterface */
    private $taxRateProvider;

    public function __construct(RepositoryInterface $adjustmentRepository, TaxRateProviderInterface $taxRateProvider)
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->taxRateProvider = $taxRateProvider;
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

        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);
        Assert::lessThanEq($shipmentRefund->total(), $shipment->getAdjustmentsTotal());

        $grossValue = $shipmentRefund->total();
        $taxAmount = (int) ($grossValue * $shipment->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT) / $shipment->getAdjustmentsTotal());
        $netValue = $grossValue - $taxAmount;

        return new LineItem(
            $shippingAdjustment->getLabel(),
            1,
            $netValue,
            $grossValue,
            $netValue,
            $grossValue,
            $taxAmount,
            $this->taxRateProvider->provide($shipment)
        );
    }
}
