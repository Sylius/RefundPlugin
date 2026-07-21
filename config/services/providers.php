<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_refund.supported_gateways', ['offline']);

    $services->set('sylius_refund.provider.credit_memo_file', \Sylius\RefundPlugin\Provider\CreditMemoFileProvider::class)
        ->args([
            service('sylius_refund.generator.credit_memo_file_name'),
            service('sylius_refund.manager.credit_memo_file'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface::class, 'sylius_refund.provider.credit_memo_file');

    $services->set('sylius_refund.provider.refunded_shipment_fee', \Sylius\RefundPlugin\Provider\RefundedShipmentFeeProvider::class)
        ->args([service('sylius.repository.adjustment')]);

    $services->alias(\Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface::class, 'sylius_refund.provider.refunded_shipment_fee');

    $services->set('sylius_refund.provider.order_refunded_total', \Sylius\RefundPlugin\Provider\OrderRefundedTotalProvider::class)
        ->args([
            service('sylius_refund.repository.refund'),
            service('sylius.repository.order_item_unit'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface::class, 'sylius_refund.provider.order_refunded_total');

    $services->set('sylius_refund.provider.current_date_time_immutable', \Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProvider::class);

    $services->alias(\Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface::class, 'sylius_refund.provider.current_date_time_immutable');

    $services->set('sylius_refund.provider.order_item_unit_total', \Sylius\RefundPlugin\Provider\OrderItemUnitTotalProvider::class)
        ->args([service('sylius.repository.order_item_unit')])
        ->tag('sylius_refund.refund_unit_total_provider');

    $services->set('sylius_refund.provider.shipment_total', \Sylius\RefundPlugin\Provider\ShipmentTotalProvider::class)
        ->args([service('sylius.repository.adjustment')])
        ->tag('sylius_refund.refund_unit_total_provider');

    $services->set('sylius_refund.provider.remaining_total', \Sylius\RefundPlugin\Provider\RemainingTotalProvider::class)
        ->args([
            tagged_locator('sylius_refund.refund_unit_total_provider', indexAttribute: 'refund_type', defaultIndexMethod: 'refundType'),
            service('sylius_refund.repository.refund'),
        ]);

    $services->alias(\Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface::class, 'sylius_refund.provider.remaining_total');

    $services->set('sylius_refund.provider.unit_refunded_total', \Sylius\RefundPlugin\Provider\UnitRefundedTotalProvider::class)
        ->args([service('sylius_refund.repository.refund')]);

    $services->alias(\Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface::class, 'sylius_refund.provider.unit_refunded_total');

    $services->set('sylius_refund.provider.related_payment_id', \Sylius\RefundPlugin\Provider\DefaultRelatedPaymentIdProvider::class);

    $services->alias(\Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface::class, 'sylius_refund.provider.related_payment_id');

    $services->set('sylius_refund.provider.refund_payment_methods', \Sylius\RefundPlugin\Provider\SupportedRefundPaymentMethodsProvider::class)
        ->args([
            service('sylius.repository.payment_method'),
            '%sylius_refund.supported_gateways%',
        ]);

    $services->alias(\Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface::class, 'sylius_refund.provider.refund_payment_methods');

    $services->set('sylius_refund.provider.tax_rate', \Sylius\RefundPlugin\Provider\TaxRateProvider::class);

    $services->alias(\Sylius\RefundPlugin\Provider\TaxRateProviderInterface::class, 'sylius_refund.provider.tax_rate');
};
