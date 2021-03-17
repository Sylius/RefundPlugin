<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Calculator\UnitRefundTotalCalculatorInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RefundUnitsCommandCreator implements RefundUnitsCommandCreatorInterface
{
    /** @var UnitRefundTotalCalculatorInterface */
    private $unitRefundTotalCalculator;

    public function __construct(UnitRefundTotalCalculatorInterface $unitRefundTotalCalculator)
    {
        $this->unitRefundTotalCalculator = $unitRefundTotalCalculator;
    }

    public function fromRequest(Request $request): RefundUnits
    {
        Assert::true($request->attributes->has('orderNumber'), 'Refunded order number not provided');

        $units = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_units', []));
        $shipments = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_shipments', []));

        if (count($units) === 0 && count($shipments) === 0) {
            throw InvalidRefundAmount::withValidationConstraint('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        return new RefundUnits(
            $request->attributes->get('orderNumber'),
            $this->parseIdsToUnitRefunds($units, RefundType::orderItemUnit(), OrderItemUnitRefund::class),
            $this->parseIdsToUnitRefunds($shipments, RefundType::shipment(), ShipmentRefund::class),
            (int) $request->request->get('sylius_refund_payment_method'),
            $request->request->get('sylius_refund_comment', '')
        );
    }

    /**
     * Parse shipment id's to ShipmentRefund with id and remaining total or amount passed in request
     *
     * @return array|UnitRefundInterface[]
     */
    private function parseIdsToUnitRefunds(array $units, RefundType $refundType, string $unitRefundClass): array
    {
        $refundUnits = [];
        foreach ($units as $id => $unit) {
            $total = $this
                ->unitRefundTotalCalculator
                ->calculateForUnitWithIdAndType($id, $refundType, $this->getAmount($unit))
            ;

            $refundUnits[] = new $unitRefundClass((int) $id, $total);
        }

        return $refundUnits;
    }

    private function filterEmptyRefundUnits(array $units): array
    {
        return array_filter($units, function (array $refundUnit): bool {
            return
                (isset($refundUnit['amount']) && $refundUnit['amount'] !== '')
                || isset($refundUnit['full'])
            ;
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
