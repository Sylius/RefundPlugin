<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Provider\TaxRateAmountProviderInterface;
use Webmozart\Assert\Assert;

final class ShipmentLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var TaxRateAmountProviderInterface */
    private $taxRateAmountProvider;

    public function __construct(
        RepositoryInterface $adjustmentRepository,
        TaxRateAmountProviderInterface $taxRateAmountProvider
    ) {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->taxRateAmountProvider = $taxRateAmountProvider;
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

        $taxAdjustments = $shipment->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        if (count($taxAdjustments) > 1) {
            throw MoreThanOneTaxAdjustment::occur();
        }

        $taxRateAmount = 0;
        $taxAmount = 0;
        if (count($taxAdjustments) === 1) {
            /** @var AdjustmentInterface $taxAdjustment */
            $taxAdjustment = $taxAdjustments->first();
            $taxRateAmount = $this->taxRateAmountProvider->provide($taxAdjustment);
            $taxAmount = $taxAdjustment->getAmount();
        }

        $taxRate = $taxRateAmount * 100 . '%';
        $netValue = $grossValue - $taxAmount;

        return new LineItem(
            $shippingAdjustment->getLabel(),
            1,
            $netValue,
            $grossValue,
            $netValue,
            $grossValue,
            $taxAmount,
            $taxRate
        );
    }
}
