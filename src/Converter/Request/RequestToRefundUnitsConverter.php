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

use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RequestToRefundUnitsConverter implements RequestToRefundUnitsConverterInterface
{
    public function __construct(
        /** @var RequestToRefundUnitsConverterInterface[] $refundUnitsConverters */
        private iterable $refundUnitsConverters,
    ) {
    }

    public function convert(Request $request): array
    {
        $units = [];

        foreach ($this->refundUnitsConverters as $refundUnitsConverter) {
            Assert::isInstanceOf($refundUnitsConverter, RequestToRefundUnitsConverterInterface::class);

            $units = array_merge($units, $refundUnitsConverter->convert($request));
        }

        return $units;
    }
}
