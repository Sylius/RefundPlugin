<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.filter.unit_refund', \Sylius\RefundPlugin\Filter\UnitRefundFilter::class);

    $services->alias(\Sylius\RefundPlugin\Filter\UnitRefundFilterInterface::class, 'sylius_refund.filter.unit_refund');
};
