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

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Model\RefundTypeInterface;

final class RefundTypeFactory implements RefundTypeFactoryInterface
{
    public string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(string $refundType): RefundTypeInterface
    {
        return new $this->className($refundType);
    }
}
