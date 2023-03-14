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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

interface CreditMemoFactoryInterface extends FactoryInterface
{
    public function createWithData(
        OrderInterface $order,
        int $total,
        array $lineItems,
        array $taxItems,
        string $comment,
        CustomerBillingDataInterface $from,
        ?ShopBillingDataInterface $to,
    ): CreditMemoInterface;
}
