<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Filter\UnitRefundFilter;
use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.filter.unit_refund', UnitRefundFilter::class);

    $services->alias(UnitRefundFilterInterface::class, 'sylius_refund.filter.unit_refund');
};
