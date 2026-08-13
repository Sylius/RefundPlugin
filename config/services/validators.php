<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.validator.refund_units_command', \Sylius\RefundPlugin\Validator\RefundUnitsCommandValidator::class)
        ->args([
            service('sylius_refund.checker.order_refunding_availability'),
            service('sylius_refund.validator.refund_amount'),
            tagged_iterator('sylius_refund.validator.unit_refunds_belonging_to_order'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface::class, 'sylius_refund.validator.refund_units_command');

    $services->set('sylius_refund.validator.order_item_unit_refunds_belonging_to_order', \Sylius\RefundPlugin\Validator\OrderItemUnitRefundsBelongingToOrderValidator::class)
        ->args([
            service('sylius_refund.filter.unit_refund'),
            service('sylius_refund.doctrine.orm.query.count_order_item_unit_refunds_belonging_to_order'),
        ])
        ->tag('sylius_refund.validator.unit_refunds_belonging_to_order');

    $services->set('sylius_refund.validator.shipment_refunds_belonging_to_order', \Sylius\RefundPlugin\Validator\ShipmentRefundsBelongingToOrderValidator::class)
        ->args([
            service('sylius_refund.filter.unit_refund'),
            service('sylius_refund.doctrine.orm.query.count_shipment_refunds_belonging_to_order'),
        ])
        ->tag('sylius_refund.validator.unit_refunds_belonging_to_order');

    $services->set('sylius_refund.validator.refund_amount', \Sylius\RefundPlugin\Validator\RefundAmountValidator::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(\Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface::class, 'sylius_refund.validator.refund_amount');
};
