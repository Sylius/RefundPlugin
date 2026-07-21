<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.doctrine.orm.query.count_order_item_unit_refunds_belonging_to_order', \Sylius\RefundPlugin\Doctrine\ORM\CountOrderItemUnitRefundsBelongingToOrderQuery::class)
        ->args([service('sylius.repository.order_item_unit')]);

    $services->set('sylius_refund.doctrine.orm.query.count_shipment_refunds_belonging_to_order', \Sylius\RefundPlugin\Doctrine\ORM\CountShipmentRefundsBelongingToOrderQuery::class)
        ->args([service('sylius.repository.adjustment')]);
};
