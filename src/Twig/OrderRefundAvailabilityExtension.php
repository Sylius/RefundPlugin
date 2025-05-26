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

namespace Sylius\RefundPlugin\Twig;

use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderRefundAvailabilityExtension extends AbstractExtension
{
    public function __construct(
        private readonly OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('can_refund_order', [$this, 'canRefundOrder']),
        ];
    }

    public function canRefundOrder(string $orderNumber): bool
    {
        return ($this->orderRefundingAvailabilityChecker)($orderNumber);
    }
}
