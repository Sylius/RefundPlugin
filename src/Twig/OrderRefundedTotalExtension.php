<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Twig;

use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;

final class OrderRefundedTotalExtension extends \Twig_Extension
{
    /** @var OrderRefundedTotalProviderInterface */
    private $orderRefundedTotalProvider;

    public function __construct(OrderRefundedTotalProviderInterface $orderRefundedTotalProvider)
    {
        $this->orderRefundedTotalProvider = $orderRefundedTotalProvider;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function(
                'sylius_refund_order_refunded_total',
                [$this->orderRefundedTotalProvider, '__invoke']
            ),
        ];
    }
}
