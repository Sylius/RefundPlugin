<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.state_resolver.order_fully_refunded', \Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolver::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.order'),
            service('sylius_refund.checker.order_fully_refunded_total'),
            service('sylius.repository.order'),
        ]);

    $services->alias(\Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface::class, 'sylius_refund.state_resolver.order_fully_refunded');

    $services->set('sylius_refund.state_resolver.order_partially_refunded', \Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolver::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.order'),
        ]);

    $services->alias(\Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface::class, 'sylius_refund.state_resolver.order_partially_refunded');

    $services->set('sylius_refund.state_resolver.refund_payment_completed_applier', \Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplier::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius_refund.manager.refund_payment'),
        ]);

    $services->alias(\Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface::class, 'sylius_refund.state_resolver.refund_payment_completed_applier');
};
