<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Webmozart\Assert\Assert;

final class OrderShowMenuListener
{
    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundsListAvailabilityChecker;

    public function __construct(OrderRefundingAvailabilityCheckerInterface $orderRefundsListAvailabilityChecker)
    {
        $this->orderRefundsListAvailabilityChecker = $orderRefundsListAvailabilityChecker;
    }

    public function addRefundsButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        /** @var string|null $orderNumber */
        $orderNumber = $order->getNumber();
        Assert::notNull($orderNumber);

        if ($this->orderRefundsListAvailabilityChecker->__invoke($orderNumber)) {
            $menu
                ->addChild('refunds', [
                    'route' => 'sylius_refund_order_refunds_list',
                    'routeParameters' => ['orderNumber' => $orderNumber],
                ])
                ->setLabel('sylius_refund.ui.refunds')
                ->setLabelAttribute('icon', 'reply all')
                ->setLabelAttribute('color', 'grey')
            ;
        }
    }
}
