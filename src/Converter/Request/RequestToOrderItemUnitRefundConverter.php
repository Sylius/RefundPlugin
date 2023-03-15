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

namespace Sylius\RefundPlugin\Converter\Request;

use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Symfony\Component\HttpFoundation\Request;

final class RequestToOrderItemUnitRefundConverter implements RequestToRefundUnitsConverterInterface
{
    public function __construct(private RefundUnitsConverterInterface $refundUnitsConverter)
    {
    }

    /**
     * @return OrderItemUnitRefund[]
     */
    public function convert(Request $request): array
    {
        return $this->refundUnitsConverter->convert(
            $request->request->all()['sylius_refund_units'] ?? [],
            OrderItemUnitRefund::class,
        );
    }
}

class_alias(RequestToOrderItemUnitRefundConverter::class, \Sylius\RefundPlugin\Converter\RequestToOrderItemUnitRefundConverter::class);
