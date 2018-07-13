<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Component\Core\OrderPaymentStates;

final class OrderShowMenuListener
{
    public function addRefundsButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        if ($order->getPaymentState() === OrderPaymentStates::STATE_PAID) {
            $menu
                ->addChild('refunds', [
                    'route' => 'sylius_refund_order_refunds_list',
                    'routeParameters' => ['orderNumber' => $order->getNumber()],
                ])
                ->setLabel('sylius_refunds.ui.refunds')
                ->setLabelAttribute('icon', 'reply all')
                ->setLabelAttribute('color', 'grey')
            ;
        }
    }
}
