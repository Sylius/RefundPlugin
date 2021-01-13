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

namespace Tests\Sylius\RefundPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
use Sylius\RefundPlugin\Entity\AdjustmentInterface;
use Sylius\RefundPlugin\Entity\AdjustmentTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_adjustment")
 */
class Adjustment extends BaseAdjustment implements AdjustmentInterface
{
    use AdjustmentTrait;
}
