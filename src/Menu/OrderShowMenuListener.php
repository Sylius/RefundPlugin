<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;

final class OrderShowMenuListener
{
    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    public function __construct(OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker)
    {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
    }

    public function addRefundsButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        if ($this->orderRefundingAvailabilityChecker->__invoke($order->getNumber())) {
            $menu
                ->addChild('refunds', [
                    'route' => 'sylius_refund_order_refunds_list',
                    'routeParameters' => ['orderNumber' => $order->getNumber()],
                ])
                ->setLabel('sylius_refund.ui.refunds')
                ->setLabelAttribute('icon', 'reply all')
                ->setLabelAttribute('color', 'grey')
            ;
        }
    }
}
