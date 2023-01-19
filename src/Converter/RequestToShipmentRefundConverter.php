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

use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\HttpFoundation\Request;

final class RequestToShipmentRefundConverter implements RequestToRefundUnitsConverterInterface
{
    private RefundUnitsConverterInterface $refundUnitsConverter;

    public function __construct(RefundUnitsConverterInterface $refundUnitsConverter)
    {
        $this->refundUnitsConverter = $refundUnitsConverter;
    }

    /**
     * @return ShipmentRefund[]
     */
    public function convert(Request $request): array
    {
        return $this->refundUnitsConverter->convert(
            $request->request->has('sylius_refund_shipments') ? $request->request->all()['sylius_refund_shipments'] : [],
            ShipmentRefund::class,
        );
    }
}
