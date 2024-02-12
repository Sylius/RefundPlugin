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

namespace Sylius\RefundPlugin\Factory;

use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Webmozart\Assert\Assert;

final class RefundTypeFactory implements RefundTypeFactoryInterface
{
    public string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(string $refundType): RefundTypeInterface
    {
        $refundType = new $this->className($refundType);
        Assert::isInstanceOf($refundType, RefundTypeInterface::class);

        return $refundType;
    }
}
