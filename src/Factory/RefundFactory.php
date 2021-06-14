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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

final class RefundFactory implements RefundFactoryInterface
{
    /** @var string */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(): RefundInterface
    {
        throw new \InvalidArgumentException('Default creation method is forbidden for this object. Use `createWithData` instead.');
    }

    public function createWithData(OrderInterface $order, int $unitId, int $amount, RefundTypeInterface $type): RefundInterface
    {
        return new $this->className($order, $amount, $unitId, $type);
    }
}
