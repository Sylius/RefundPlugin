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

namespace Sylius\RefundPlugin\Provider;

use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class DelegatingRefundUnitTotalProvider implements DelegatingRefundUnitTotalProviderInterface
{
    public function __construct(
        private ServiceProviderInterface $refundUnitTotalProvider,
    ) {
    }

    public function getRefundUnitTotal(int $id, RefundTypeInterface $refundType): int
    {
        return $this->refundUnitTotalProvider->get($refundType->getValue())->getRefundUnitTotal($id);
    }
}
