<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Converter\LineItem\CompositeLineItemConverter;
use Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface;
use Sylius\RefundPlugin\Converter\LineItem\OrderItemUnitLineItemsConverter;
use Sylius\RefundPlugin\Converter\LineItem\ShipmentLineItemsConverter;
use Sylius\RefundPlugin\Converter\RefundUnitsConverter;
use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToOrderItemUnitRefundConverter;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverter;
use Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Converter\Request\RequestToShipmentRefundConverter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.converter.line_items', CompositeLineItemConverter::class)
        ->args([
            tagged_iterator('sylius_refund.line_item_converter'),
            service('sylius_refund.filter.unit_refund'),
        ]);

    $services->alias(LineItemsConverterInterface::class, 'sylius_refund.converter.line_items');

    $services->alias('sylius_refund.converter.line_items.composite', 'sylius_refund.converter.line_items');

    $services->set('sylius_refund.converter.request_to_refund_units', RequestToRefundUnitsConverter::class)
        ->args([tagged_iterator('sylius_refund.request_to_refund_units_converter')]);

    $services->alias(RequestToRefundUnitsConverterInterface::class, 'sylius_refund.converter.request_to_refund_units');

    $services->set('sylius_refund.converter.request_to_shipment_refund', RequestToShipmentRefundConverter::class)
        ->args([service('sylius_refund.converter.refund_units')])
        ->tag('sylius_refund.request_to_refund_units_converter');

    $services->alias(RequestToShipmentRefundConverter::class, 'sylius_refund.converter.request_to_shipment_refund');

    $services->set('sylius_refund.converter.request_to_order_item_unit_refund', RequestToOrderItemUnitRefundConverter::class)
        ->args([service('sylius_refund.converter.refund_units')])
        ->tag('sylius_refund.request_to_refund_units_converter');

    $services->alias(RequestToOrderItemUnitRefundConverter::class, 'sylius_refund.converter.request_to_order_item_unit_refund');

    $services->set('sylius_refund.converter.line_items.order_item_unit', OrderItemUnitLineItemsConverter::class)
        ->args([
            service('sylius.repository.order_item_unit'),
            service('sylius_refund.provider.tax_rate'),
            service('sylius_refund.factory.line_item'),
        ])
        ->tag('sylius_refund.line_item_converter');

    $services->set('sylius_refund.converter.line_items.shipment', ShipmentLineItemsConverter::class)
        ->args([
            service('sylius.repository.adjustment'),
            service('sylius_refund.provider.tax_rate'),
            service('sylius_refund.factory.line_item'),
        ])
        ->tag('sylius_refund.line_item_converter');

    $services->set('sylius_refund.converter.refund_units', RefundUnitsConverter::class)
        ->args([service('sylius_refund.calculator.unit_refund_total')]);

    $services->alias(RefundUnitsConverterInterface::class, 'sylius_refund.converter.refund_units');
};
