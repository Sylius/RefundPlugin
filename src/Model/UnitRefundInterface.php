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

namespace Sylius\RefundPlugin\Model;

interface UnitRefundInterface
{
    public function id(): int;

    public function total(): int;

    public static function type(): RefundType;
}
