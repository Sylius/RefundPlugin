<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Prooph\Common\Messaging\Command;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreator implements CommandCreatorInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    public function __construct(RemainingTotalProviderInterface $remainingTotalProvider)
    {
        $this->remainingTotalProvider = $remainingTotalProvider;
    }

    public function fromRequest(Request $request): Command
    {
        if (!$request->attributes->has('orderNumber')) {
            throw new \InvalidArgumentException('Refunded order number not provided');
        }

        $units = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_units', []));
        $shipments = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_shipments', []));

        if (count($units) === 0 && count($shipments) === 0) {
            throw new \InvalidArgumentException('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        return new RefundUnits(
            $request->attributes->get('orderNumber'),
            $this->parseIdsToUnitRefunds($units),
            $this->parseIdsToShipmentRefunds($shipments),
            (int) $request->request->get('sylius_refund_payment_method'),
            $request->request->get('sylius_refund_comment', '')
        );
    }

    /**
     * Parse unit id's to UnitRefund with id and remaining total or amount passed in request
     *
     * @return array|UnitRefundInterface[]
     */
    private function parseIdsToUnitRefunds(array $units): array
    {
        return array_map(function (array $refundUnit): UnitRefundInterface {
            if (isset($refundUnit['amount']) && $refundUnit['amount'] !== '') {
                $id = (int) $refundUnit['partial-id'];
                $total = (int) (((float) $refundUnit['amount']) * 100);

                return new OrderItemUnitRefund($id, $total);
            }

            $id = (int) $refundUnit['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::orderItemUnit());

            return new OrderItemUnitRefund($id, $total);
        }, $units);
    }

    /**
     * Parse shipment id's to ShipmentRefund with id and remaining total or amount passed in request
     *
     * @return array|UnitRefundInterface[]
     */
    private function parseIdsToShipmentRefunds(array $units): array
    {
        return array_map(function (array $refundShipment): UnitRefundInterface {
            if (isset($refundShipment['amount']) && $refundShipment['amount'] !== '') {
                $id = (int) $refundShipment['partial-id'];
                $total = (int) (((float) $refundShipment['amount']) * 100);

                return new ShipmentRefund($id, $total);
            }

            $id = (int) $refundShipment['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::shipment());

            return new ShipmentRefund($id, $total);
        }, $units);
    }

    private function filterEmptyRefundUnits(array $units): array
    {
        return array_filter($units, function (array $refundUnit): bool {
            return
                (isset($refundUnit['amount']) && $refundUnit['amount'] !== '')
                || isset($refundUnit['id'])
            ;
        });
    }
}
