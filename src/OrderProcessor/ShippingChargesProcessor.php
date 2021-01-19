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

namespace Sylius\RefundPlugin\OrderProcessor;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
final class ShippingChargesProcessor implements OrderProcessorInterface
{
    /** @var FactoryInterface */
    private $adjustmentFactory;

    /** @var DelegatingCalculatorInterface */
    private $shippingChargesCalculator;

    public function __construct(
        FactoryInterface $adjustmentFactory,
        DelegatingCalculatorInterface $shippingChargesCalculator
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->shippingChargesCalculator = $shippingChargesCalculator;
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            $shipment->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

            try {
                $shippingCharge = $this->shippingChargesCalculator->calculate($shipment);
                $shippingMethod = $shipment->getMethod();
                Assert::notNull($shippingMethod);

                /** @var AdjustmentInterface $adjustment */
                $adjustment = $this->adjustmentFactory->createNew();
                $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
                $adjustment->setAmount($shippingCharge);
                $adjustment->setLabel($shippingMethod->getName());
                $adjustment->setDetails([
                    'shippingMethodCode' => $shippingMethod->getCode(),
                    'shippingMethodName' => $shippingMethod->getName(),
                ]);

                $shipment->addAdjustment($adjustment);
            } catch (UndefinedShippingMethodException $exception) {
            }
        }
    }
}
