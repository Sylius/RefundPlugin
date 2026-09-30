<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\CreditMemoContext as ApplicationCreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\RefundingContext as ApplicationRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext as SetupRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Transform\PriceContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('application_refunds'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
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
                PriceContext::class,
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.zone',
                ChannelContext::class,
                SetupRefundingContext::class,
            )
            ->withContexts(
                ApplicationCreditMemoContext::class,
                EmailsContext::class,
                ApplicationRefundingContext::class,
            )
            ->withFilter(new TagFilter('@refunds&&@application')),
        ),
    )
;
