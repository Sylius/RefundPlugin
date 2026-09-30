<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext as SetupRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer\CreditMemoContext as ShopCustomerCreditMemoContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('customer_credit_memos'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                CreditMemosContext::class,
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.tax_rate',
                'sylius.behat.context.transform.user',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
                ChannelContext::class,
                SetupRefundingContext::class,
            )
            ->withContexts(
                'sylius.behat.context.ui.shop.account',
            )
            ->withContexts(
                EmailsContext::class,
                ShopCustomerCreditMemoContext::class,
            )
            ->withFilter(new TagFilter('@customer_credit_memos&&@ui')),
        ),
    )
;
