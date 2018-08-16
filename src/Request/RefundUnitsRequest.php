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
            self::parseIdsToIntegers($request->request->get('sylius_refund_units', [])),
            self::parseIdsToIntegers($request->request->get('sylius_refund_shipments', [])),
            $request->request->get('sylius_refund_payment_method')
        );
    }

    private static function parseIdsToIntegers(array $elements): array
    {
        return array_map(function (string $element): int {
            return (int) $element;
        }, $elements);
    }
}
