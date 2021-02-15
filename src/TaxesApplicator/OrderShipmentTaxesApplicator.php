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

namespace Sylius\RefundPlugin\TaxesApplicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\ShipmentInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
final class OrderShipmentTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /** @var CalculatorInterface */
    private $calculator;

    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    /** @var TaxRateResolverInterface */
    private $taxRateResolver;

    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->taxRateResolver = $taxRateResolver;
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        if (0 === $order->getShippingTotal()) {
            return;
        }

        if (!$order->hasShipments()) {
            throw new \LogicException('Order should have at least one shipment.');
        }

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            $shippingMethod = $this->getShippingMethod($shipment);

            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($shippingMethod, ['zone' => $zone]);
            if (null === $taxRate) {
                return;
            }

            $taxAmount = $this->calculator->calculate($shipment->getAdjustmentsTotal(), $taxRate);
            if (0.00 === $taxAmount) {
                return;
            }

            $this->addAdjustment($shipment, (int) $taxAmount, $taxRate, $shippingMethod);
        }
    }

    private function addAdjustment(
        ShipmentInterface $shipment,
        int $taxAmount,
        TaxRateInterface $taxRate,
        ShippingMethodInterface $shippingMethod
    ): void {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createWithData(
            AdjustmentInterface::TAX_ADJUSTMENT,
            $taxRate->getLabel(),
            $taxAmount,
            $taxRate->isIncludedInPrice()
        );
        $adjustment->setDetails([
            'shippingMethodCode' => $shippingMethod->getCode(),
            'shippingMethodName' => $shippingMethod->getName(),
            'taxRateCode' => $taxRate->getCode(),
            'taxRateName' => $taxRate->getName(),
            'taxRateAmount' => $taxRate->getAmount(),
        ]);

        $shipment->addAdjustment($adjustment);
    }

    /**
     * @throws \LogicException
     */
    private function getShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        $method = $shipment->getMethod();

        /** @var ShippingMethodInterface $method */
        Assert::isInstanceOf($method, ShippingMethodInterface::class);

        return $method;
    }
}
