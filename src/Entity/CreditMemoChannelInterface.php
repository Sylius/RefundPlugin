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

namespace Sylius\RefundPlugin\Entity;

interface CreditMemoChannelInterface
{
    public function getId(): int;

    public function code(): string;

    public function name(): string;

    public function color(): string;
}
