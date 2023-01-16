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

use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Symfony\Component\HttpFoundation\Request;

final class RequestToOrderItemUnitRefundConverter implements RequestToRefundUnitsConverterInterface
{
    private RefundUnitsConverterInterface $refundUnitsConverter;

    public function __construct(RefundUnitsConverterInterface $refundUnitsConverter)
    {
        $this->refundUnitsConverter = $refundUnitsConverter;
    }

    /**
     * @return OrderItemUnitRefund[]
     */
    public function convert(Request $request): array
    {
        $units = $this->refundUnitsConverter->convert(
            $request->request->has('sylius_refund_units') ? $request->request->all()['sylius_refund_units'] : [],
            RefundType::orderItemUnit(),
            OrderItemUnitRefund::class,
        );

        return $units;
    }
}
