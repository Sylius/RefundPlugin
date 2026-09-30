<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Creator\RefundCreator;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator;
use Sylius\RefundPlugin\Creator\RequestCommandCreatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.creator.refund', RefundCreator::class)
        ->args([
            service('sylius_refund.factory.refund'),
            service('sylius_refund.provider.remaining_total'),
            service('sylius.repository.order'),
            service('sylius_refund.manager.refund'),
        ]);

    $services->alias(RefundCreatorInterface::class, 'sylius_refund.creator.refund');

    $services->set('sylius_refund.creator.request_command', RefundUnitsCommandCreator::class)
        ->args([service('sylius_refund.converter.request_to_refund_units')]);

    $services->alias(RequestCommandCreatorInterface::class, 'sylius_refund.creator.request_command');
};
