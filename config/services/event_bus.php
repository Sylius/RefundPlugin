<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Listener\CreditMemoGeneratedEventListener;
use Sylius\RefundPlugin\Listener\UnitRefundedEventListener;
use Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager;
use Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManager;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManagerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.listener.credit_memo_generated', CreditMemoGeneratedEventListener::class)
        ->public()
        ->args([service('sylius.command_bus')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius_refund.listener.unit_refunded', UnitRefundedEventListener::class)
        ->public()
        ->args([service('sylius_refund.state_resolver.order_partially_refunded')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius_refund.process_manager.units_refunded', UnitsRefundedProcessManager::class)
        ->public()
        ->args([tagged_iterator('sylius_refund.units_refunded.process_step')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->alias(UnitsRefundedProcessManagerInterface::class, 'sylius_refund.process_manager.units_refunded');

    $services->set('sylius_refund.process_manager.refund_payment', RefundPaymentProcessManager::class)
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

    $services->set('sylius_refund.process_manager.credit_memo', CreditMemoProcessManager::class)
        ->args([service('sylius.command_bus')])
        ->tag('sylius_refund.units_refunded.process_step', ['priority' => 100]);
};
