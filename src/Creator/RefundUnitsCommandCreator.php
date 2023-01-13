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

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RefundUnitsCommandCreator implements RefundUnitsCommandCreatorInterface
{
    private RefundUnitsConverterInterface $refundUnitsConverter;

    public function __construct(RefundUnitsConverterInterface $refundUnitsConverter)
    {
        $this->refundUnitsConverter = $refundUnitsConverter;
    }

    public function fromRequest(Request $request): RefundUnits
    {
        Assert::true($request->attributes->has('orderNumber'), 'Refunded order number not provided');

        $units = $this->convertRequestToRefundUnits($request);

        if (count($units) === 0) {
            throw InvalidRefundAmount::withValidationConstraint('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        /** @var string $comment */
        $comment = $request->request->get('sylius_refund_comment', '');

        return new RefundUnits(
            $request->attributes->get('orderNumber'),
            $units,
            (int) $request->request->get('sylius_refund_payment_method'),
            $comment,
        );
    }

    /**
     * @return array|RefundUnits[]
     */
    private function convertRequestToRefundUnits(Request $request): array
    {
        $units = $this->refundUnitsConverter->convert(
            $request->request->has('sylius_refund_units') ? $request->request->all()['sylius_refund_units'] : [],
            RefundType::orderItemUnit(),
            OrderItemUnitRefund::class,
        );

        $shipments = $this->refundUnitsConverter->convert(
            $request->request->has('sylius_refund_shipments') ? $request->request->all()['sylius_refund_shipments'] : [],
            RefundType::shipment(),
            ShipmentRefund::class,
        );

        return array_merge($units, $shipments);
    }
}
