<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Request;

use Sylius\RefundPlugin\Command\RefundUnits;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsRequest
{
    public static function getCommand(Request $request): RefundUnits
    {
        if ($request->request->get('sylius_refund_units') === null && $request->request->get('sylius_refund_shipments') === null) {
            throw new \InvalidArgumentException('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        return new RefundUnits(
            $request->attributes->get('orderNumber'),
            array_map(function (string $unitId): int {
                return (int) $unitId;
            }, $request->request->get('sylius_refund_units', [])),
            array_map(function (string $unitId): int {
                return (int) $unitId;
            }, $request->request->get('sylius_refund_shipments', []))
        );
    }
}
