<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\RefundPlugin\Provider\CreditMemoFileProvider;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProvider;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Sylius\RefundPlugin\Provider\DefaultRelatedPaymentIdProvider;
use Sylius\RefundPlugin\Provider\OrderItemUnitTotalProvider;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProvider;
use Sylius\RefundPlugin\Provider\OrderRefundedTotalProviderInterface;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProvider;
use Sylius\RefundPlugin\Provider\RefundedShipmentFeeProviderInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProvider;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Sylius\RefundPlugin\Provider\ShipmentTotalProvider;
use Sylius\RefundPlugin\Provider\SupportedRefundPaymentMethodsProvider;
use Sylius\RefundPlugin\Provider\TaxRateProvider;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProvider;
use Sylius\RefundPlugin\Provider\UnitRefundedTotalProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_refund.supported_gateways', ['offline']);

    $services->set('sylius_refund.provider.credit_memo_file', CreditMemoFileProvider::class)
        ->args([
            service('sylius_refund.generator.credit_memo_file_name'),
            service('sylius_refund.manager.credit_memo_file'),
        ]);

    $services->alias(CreditMemoFileProviderInterface::class, 'sylius_refund.provider.credit_memo_file');

    $services->set('sylius_refund.provider.refunded_shipment_fee', RefundedShipmentFeeProvider::class)
        ->args([service('sylius.repository.adjustment')]);

    $services->alias(RefundedShipmentFeeProviderInterface::class, 'sylius_refund.provider.refunded_shipment_fee');

    $services->set('sylius_refund.provider.order_refunded_total', OrderRefundedTotalProvider::class)
        ->args([
            service('sylius_refund.repository.refund'),
            service('sylius.repository.order_item_unit'),
        ]);

    $services->alias(OrderRefundedTotalProviderInterface::class, 'sylius_refund.provider.order_refunded_total');

    $services->set('sylius_refund.provider.current_date_time_immutable', CurrentDateTimeImmutableProvider::class);

    $services->alias(CurrentDateTimeImmutableProviderInterface::class, 'sylius_refund.provider.current_date_time_immutable');

    $services->set('sylius_refund.provider.order_item_unit_total', OrderItemUnitTotalProvider::class)
        ->args([service('sylius.repository.order_item_unit')])
        ->tag('sylius_refund.refund_unit_total_provider');

    $services->set('sylius_refund.provider.shipment_total', ShipmentTotalProvider::class)
        ->args([service('sylius.repository.adjustment')])
        ->tag('sylius_refund.refund_unit_total_provider');

    $services->set('sylius_refund.provider.remaining_total', RemainingTotalProvider::class)
        ->args([
            tagged_locator('sylius_refund.refund_unit_total_provider', indexAttribute: 'refund_type', defaultIndexMethod: 'refundType'),
            service('sylius_refund.repository.refund'),
        ]);

    $services->alias(RemainingTotalProviderInterface::class, 'sylius_refund.provider.remaining_total');

    $services->set('sylius_refund.provider.unit_refunded_total', UnitRefundedTotalProvider::class)
        ->args([service('sylius_refund.repository.refund')]);

    $services->alias(UnitRefundedTotalProviderInterface::class, 'sylius_refund.provider.unit_refunded_total');

    $services->set('sylius_refund.provider.related_payment_id', DefaultRelatedPaymentIdProvider::class);

    $services->alias(RelatedPaymentIdProviderInterface::class, 'sylius_refund.provider.related_payment_id');

    $services->set('sylius_refund.provider.refund_payment_methods', SupportedRefundPaymentMethodsProvider::class)
        ->args([
            service('sylius.repository.payment_method'),
            '%sylius_refund.supported_gateways%',
        ]);

    $services->alias(RefundPaymentMethodsProviderInterface::class, 'sylius_refund.provider.refund_payment_methods');

    $services->set('sylius_refund.provider.tax_rate', TaxRateProvider::class);

    $services->alias(TaxRateProviderInterface::class, 'sylius_refund.provider.tax_rate');
};
