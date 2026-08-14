<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationChecker;
use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker;
use Sylius\RefundPlugin\Checker\OrderRefundsListAvailabilityChecker;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityChecker;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.checker.credit_memo_customer_relation', CreditMemoCustomerRelationChecker::class)
        ->args([
            service('sylius.context.customer'),
            service('sylius_refund.repository.credit_memo'),
        ]);

    $services->alias(CreditMemoCustomerRelationCheckerInterface::class, 'sylius_refund.checker.credit_memo_customer_relation');

    $services->set('sylius_refund.checker.order_refunding_availability', OrderRefundingAvailabilityChecker::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set('sylius_refund.checker.order_refunds_list_availability', OrderRefundsListAvailabilityChecker::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.checker.order_refunding_availability'),
        ]);

    $services->set('sylius_refund.checker.order_fully_refunded_total', OrderFullyRefundedTotalChecker::class)
        ->args([service('sylius_refund.provider.order_refunded_total')]);

    $services->alias(OrderFullyRefundedTotalCheckerInterface::class, 'sylius_refund.checker.order_fully_refunded_total');

    $services->set('sylius_refund.checker.unit_refunding_availability', UnitRefundingAvailabilityChecker::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(UnitRefundingAvailabilityCheckerInterface::class, 'sylius_refund.checker.unit_refunding_availability');
};
