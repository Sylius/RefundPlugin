<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius_refund.converter.line_items', \Sylius\RefundPlugin\Converter\LineItem\CompositeLineItemConverter::class)
        ->args([
            tagged_iterator('sylius_refund.line_item_converter'),
            service('sylius_refund.filter.unit_refund'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Converter\LineItem\LineItemsConverterInterface::class, 'sylius_refund.converter.line_items');

    $services->alias('sylius_refund.converter.line_items.composite', 'sylius_refund.converter.line_items');

    $services->set('sylius_refund.converter.request_to_refund_units', \Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverter::class)
        ->args([tagged_iterator('sylius_refund.request_to_refund_units_converter')]);

    $services->alias(\Sylius\RefundPlugin\Converter\Request\RequestToRefundUnitsConverterInterface::class, 'sylius_refund.converter.request_to_refund_units');

    $services->set('sylius_refund.converter.request_to_shipment_refund', \Sylius\RefundPlugin\Converter\Request\RequestToShipmentRefundConverter::class)
        ->args([service('sylius_refund.converter.refund_units')])
        ->tag('sylius_refund.request_to_refund_units_converter');

    $services->alias(\Sylius\RefundPlugin\Converter\Request\RequestToShipmentRefundConverter::class, 'sylius_refund.converter.request_to_shipment_refund');

    $services->set('sylius_refund.converter.request_to_order_item_unit_refund', \Sylius\RefundPlugin\Converter\Request\RequestToOrderItemUnitRefundConverter::class)
        ->args([service('sylius_refund.converter.refund_units')])
        ->tag('sylius_refund.request_to_refund_units_converter');

    $services->alias(\Sylius\RefundPlugin\Converter\Request\RequestToOrderItemUnitRefundConverter::class, 'sylius_refund.converter.request_to_order_item_unit_refund');

    $services->set('sylius_refund.converter.line_items.order_item_unit', \Sylius\RefundPlugin\Converter\LineItem\OrderItemUnitLineItemsConverter::class)
        ->args([
            service('sylius.repository.order_item_unit'),
            service('sylius_refund.provider.tax_rate'),
            service('sylius_refund.factory.line_item'),
        ])
        ->tag('sylius_refund.line_item_converter');

    $services->set('sylius_refund.converter.line_items.shipment', \Sylius\RefundPlugin\Converter\LineItem\ShipmentLineItemsConverter::class)
        ->args([
            service('sylius.repository.adjustment'),
            service('sylius_refund.provider.tax_rate'),
            service('sylius_refund.factory.line_item'),
        ])
        ->tag('sylius_refund.line_item_converter');

    $services->set('sylius_refund.converter.refund_units', \Sylius\RefundPlugin\Converter\RefundUnitsConverter::class)
        ->args([service('sylius_refund.calculator.unit_refund_total')]);

    $services->alias(\Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface::class, 'sylius_refund.converter.refund_units');
};
