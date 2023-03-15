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
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RefundUnitsCommandCreator implements RequestCommandCreatorInterface
{
    public function __construct(private RequestToRefundUnitsConverterInterface|RefundUnitsConverterInterface $requestToRefundUnitsConverter)
    {
        if ($requestToRefundUnitsConverter instanceof RefundUnitsConverterInterface) {
            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing a "%s" as a 1st argument of "%s" constructor is deprecated and will be removed in 2.0.', RefundUnitsConverterInterface::class, self::class));
        }
    }

    public function fromRequest(Request $request): RefundUnits
    {
        Assert::true($request->attributes->has('orderNumber'), 'Refunded order number not provided');

        if ($this->requestToRefundUnitsConverter instanceof RefundUnitsConverterInterface) {
            /** @phpstan-ignore-next-line */
            $units = $this->requestToRefundUnitsConverter->convert(
                $request->request->has('sylius_refund_units') ? $request->request->all()['sylius_refund_units'] : [],
                /** @phpstan-ignore-next-line */
                RefundType::orderItemUnit(),
                OrderItemUnitRefund::class,
            );

            /** @phpstan-ignore-next-line */
            $shipments = $this->requestToRefundUnitsConverter->convert(
                $request->request->has('sylius_refund_shipments') ? $request->request->all()['sylius_refund_shipments'] : [],
                /** @phpstan-ignore-next-line */
                RefundType::shipment(),
                ShipmentRefund::class,
            );

            $units = array_merge($units, $shipments);
        } else {
            $units = $this->requestToRefundUnitsConverter->convert($request);
        }

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
}
