<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.checker.credit_memo_customer_relation', \Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationChecker::class)
        ->args([
            service('sylius.context.customer'),
            service('sylius_refund.repository.credit_memo'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface::class, 'sylius_refund.checker.credit_memo_customer_relation');

    $services->set('sylius_refund.checker.order_refunding_availability', \Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityChecker::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set('sylius_refund.checker.order_refunds_list_availability', \Sylius\RefundPlugin\Checker\OrderRefundsListAvailabilityChecker::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.checker.order_refunding_availability'),
        ]);

    $services->set('sylius_refund.checker.order_fully_refunded_total', \Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalChecker::class)
        ->args([service('sylius_refund.provider.order_refunded_total')]);

    $services->alias(\Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface::class, 'sylius_refund.checker.order_fully_refunded_total');

    $services->set('sylius_refund.checker.unit_refunding_availability', \Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityChecker::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(\Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface::class, 'sylius_refund.checker.unit_refunding_availability');
};
