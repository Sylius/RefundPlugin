<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class ShipmentLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function convert(array $units): Collection
    {
        $lineItems = new ArrayCollection();

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            /** @var AdjustmentInterface $shippingAdjustment */
            $shippingAdjustment = $this
                ->adjustmentRepository
                ->findOneBy(['id' => $unitRefund->id(), 'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT])
            ;
            Assert::notNull($shippingAdjustment);
            Assert::lessThanEq($unitRefund->total(), $shippingAdjustment->getAmount());

            $lineItems->add(new LineItem(
                $shippingAdjustment->getLabel(),
                1,
                $unitRefund->total(),
                $unitRefund->total(),
                $unitRefund->total(),
                $unitRefund->total(),
                0
            ));
        }

        return $lineItems;
    }
}
