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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 *
 * This class is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
final class OrderItemsTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /** @var CalculatorInterface */
    private $calculator;

    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    /** @var IntegerDistributorInterface */
    private $distributor;

    /** @var TaxRateResolverInterface */
    private $taxRateResolver;

    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
        $this->taxRateResolver = $taxRateResolver;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        foreach ($order->getItems() as $item) {
            $quantity = $item->getQuantity();
            Assert::notSame($quantity, 0, 'Cannot apply tax to order item with 0 quantity.');

            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

            $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);
            $splitTaxes = $this->distributor->distribute($totalTaxAmount, $quantity);

            $i = 0;
            /** @var OrderItemUnitInterface $unit */
            foreach ($item->getUnits() as $unit) {
                if (0 === $splitTaxes[$i]) {
                    continue;
                }

                $this->addAdjustment($unit, $splitTaxes[$i], $taxRate);
                ++$i;
            }
        }
    }

    private function addAdjustment(OrderItemUnitInterface $unit, int $taxAmount, TaxRateInterface $taxRate): void
    {
        /** @var AdjustmentInterface $unitTaxAdjustment */
        $unitTaxAdjustment = $this->adjustmentFactory->createWithData(
            AdjustmentInterface::TAX_ADJUSTMENT,
            $taxRate->getLabel(),
            $taxAmount,
            $taxRate->isIncludedInPrice()
        );
        $unitTaxAdjustment->setDetails([
            'taxRateCode' => $taxRate->getCode(),
            'taxRateName' => $taxRate->getName(),
            'taxRateAmount' => $taxRate->getAmount(),
        ]);

        $unit->addAdjustment($unitTaxAdjustment);
    }
}
