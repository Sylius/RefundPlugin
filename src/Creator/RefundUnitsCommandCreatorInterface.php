<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated since 1.4, to be removed in 2.0, use Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface instead
 */
interface RefundUnitsCommandCreatorInterface
{
    public function fromRequest(Request $request): RefundUnits;
}
