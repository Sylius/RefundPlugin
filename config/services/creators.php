<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.creator.refund', \Sylius\RefundPlugin\Creator\RefundCreator::class)
        ->args([
            service('sylius_refund.factory.refund'),
            service('sylius_refund.provider.remaining_total'),
            service('sylius.repository.order'),
            service('sylius_refund.manager.refund'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Creator\RefundCreatorInterface::class, 'sylius_refund.creator.refund');

    $services->set('sylius_refund.creator.request_command', \Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator::class)
        ->args([service('sylius_refund.converter.request_to_refund_units')]);

    $services->alias(\Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface::class, 'sylius_refund.creator.request_command');
};
