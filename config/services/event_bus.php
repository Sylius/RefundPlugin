<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.listener.credit_memo_generated', \Sylius\RefundPlugin\Listener\CreditMemoGeneratedEventListener::class)
        ->public()
        ->args([service('sylius.command_bus')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius_refund.listener.unit_refunded', \Sylius\RefundPlugin\Listener\UnitRefundedEventListener::class)
        ->public()
        ->args([service('sylius_refund.state_resolver.order_partially_refunded')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius_refund.process_manager.units_refunded', \Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManager::class)
        ->public()
        ->args([tagged_iterator('sylius_refund.units_refunded.process_step')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->alias(\Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManagerInterface::class, 'sylius_refund.process_manager.units_refunded');

    $services->set('sylius_refund.process_manager.refund_payment', \Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager::class)
        ->args([
            service('sylius_refund.state_resolver.order_fully_refunded'),
            service('sylius_refund.provider.related_payment_id'),
            service('sylius_refund.factory.refund_payment'),
            service('sylius.repository.order'),
            service('sylius.repository.payment_method'),
            service('doctrine.orm.default_entity_manager'),
            service('sylius.event_bus'),
        ])
        ->tag('sylius_refund.units_refunded.process_step', ['priority' => 50]);

    $services->set('sylius_refund.process_manager.credit_memo', \Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager::class)
        ->args([service('sylius.command_bus')])
        ->tag('sylius_refund.units_refunded.process_step', ['priority' => 100]);
};
