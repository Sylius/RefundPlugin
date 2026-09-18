<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\OrderContext as SetupOrderContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\PaymentContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ProductContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext as SetupRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Transform\OrderContext as TransformOrderContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\CreditMemoContext as UiCreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\ManagingOrdersContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\RefundingContext as UiRefundingContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_refunds'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.mailer',
                CreditMemosContext::class,
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.tax_rate',
                'sylius.behat.context.transform.zone',
                TransformOrderContext::class,
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.zone',
                ChannelContext::class,
                SetupOrderContext::class,
                ProductContext::class,
                SetupRefundingContext::class,
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.managing_orders',
            )
            ->withContexts(
                EmailsContext::class,
                PaymentContext::class,
                UiCreditMemoContext::class,
                ManagingOrdersContext::class,
                UiRefundingContext::class,
            )
            ->withFilter(new TagFilter('@refunds&&@ui')),
        ),
    )
;
