<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Validator\OrderItemUnitRefundsBelongingToOrderValidator;
use Sylius\RefundPlugin\Validator\RefundAmountValidator;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidator;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Sylius\RefundPlugin\Validator\ShipmentRefundsBelongingToOrderValidator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.validator.refund_units_command', RefundUnitsCommandValidator::class)
        ->args([
            service('sylius_refund.checker.order_refunding_availability'),
            service('sylius_refund.validator.refund_amount'),
            tagged_iterator('sylius_refund.validator.unit_refunds_belonging_to_order'),
        ]);

    $services->alias(RefundUnitsCommandValidatorInterface::class, 'sylius_refund.validator.refund_units_command');

    $services->set('sylius_refund.validator.order_item_unit_refunds_belonging_to_order', OrderItemUnitRefundsBelongingToOrderValidator::class)
        ->args([
            service('sylius_refund.filter.unit_refund'),
            service('sylius_refund.doctrine.orm.query.count_order_item_unit_refunds_belonging_to_order'),
        ])
        ->tag('sylius_refund.validator.unit_refunds_belonging_to_order');

    $services->set('sylius_refund.validator.shipment_refunds_belonging_to_order', ShipmentRefundsBelongingToOrderValidator::class)
        ->args([
            service('sylius_refund.filter.unit_refund'),
            service('sylius_refund.doctrine.orm.query.count_shipment_refunds_belonging_to_order'),
        ])
        ->tag('sylius_refund.validator.unit_refunds_belonging_to_order');

    $services->set('sylius_refund.validator.refund_amount', RefundAmountValidator::class)
        ->args([service('sylius_refund.provider.remaining_total')]);

    $services->alias(RefundAmountValidatorInterface::class, 'sylius_refund.validator.refund_amount');
};
