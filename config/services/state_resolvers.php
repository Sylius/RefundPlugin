<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolver;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolver;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplier;
use Sylius\RefundPlugin\StateResolver\RefundPaymentCompletedStateApplierInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.state_resolver.order_fully_refunded', OrderFullyRefundedStateResolver::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.order'),
            service('sylius_refund.checker.order_fully_refunded_total'),
            service('sylius.repository.order'),
        ]);

    $services->alias(OrderFullyRefundedStateResolverInterface::class, 'sylius_refund.state_resolver.order_fully_refunded');

    $services->set('sylius_refund.state_resolver.order_partially_refunded', OrderPartiallyRefundedStateResolver::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.order'),
        ]);

    $services->alias(OrderPartiallyRefundedStateResolverInterface::class, 'sylius_refund.state_resolver.order_partially_refunded');

    $services->set('sylius_refund.state_resolver.refund_payment_completed_applier', RefundPaymentCompletedStateApplier::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius_refund.manager.refund_payment'),
        ]);

    $services->alias(RefundPaymentCompletedStateApplierInterface::class, 'sylius_refund.state_resolver.refund_payment_completed_applier');
};
