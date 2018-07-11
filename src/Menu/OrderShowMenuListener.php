<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;

final class OrderShowMenuListener
{
    public function addRefundsButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        $menu
            ->addChild('refunds', [
                'route' => 'sylius_refund_order_refunds_list',
                'routeParameters' => ['orderNumber' => $order->getNumber()],
            ])
            ->setLabel('sylius_refunds.ui.refunds')
            ->setLabelAttribute('icon', 'money bill alternate outline')
            ->setLabelAttribute('color', 'green')
        ;
    }
}
