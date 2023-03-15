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

use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestToRefundUnitsConverterInterface
{
    /**
     * @return UnitRefundInterface[]
     */
    public function convert(Request $request): array;
}
