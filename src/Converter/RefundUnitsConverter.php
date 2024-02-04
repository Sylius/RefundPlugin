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

namespace Sylius\RefundPlugin\Converter;

use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class RefundUnitsConverter implements RefundUnitsConverterInterface
{
    public function __construct(private UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator)
    {
    }

    /** @return UnitRefundInterface[] */
    public function convert(array $units, string $unitRefundClass): array
    {
        /** @var class-string|UnitRefundInterface $unitRefundClass */
        Assert::isAOf($unitRefundClass, UnitRefundInterface::class);

        $units = $this->filterEmptyRefundUnits($units);
        $refundUnits = [];
        foreach ($units as $id => $unit) {
            $total = $this
                ->unitRefundTotalCalculator
                ->calculateForUnitWithIdAndType(
                    $id,
                    $unitRefundClass::type(),
                    $this->getAmount($unit),
                )
            ;

            $unitRefund = new $unitRefundClass((int) $id, $total);
            Assert::isInstanceOf($unitRefund, UnitRefundInterface::class);

            $refundUnits[] = $unitRefund;
        }

        return $refundUnits;
    }

    private function filterEmptyRefundUnits(array $units): array
    {
        return array_filter($units, function (array $refundUnit): bool {
            return (isset($refundUnit['amount']) && $refundUnit['amount'] !== '') || isset($refundUnit['full']);
        });
    }

    private function getAmount(array $unit): ?float
    {
        if (isset($unit['full'])) {
            return null;
        }

        Assert::keyExists($unit, 'amount');

        return (float) $unit['amount'];
    }
}
