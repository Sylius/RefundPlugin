<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FriendsOfBehat\PageObjectExtension\Element\Element;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\CreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\RefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\OrderContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\PaymentContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ProductContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Transform\PriceContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\ManagingOrdersContext;
use Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElement;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order\ShowPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\OrderRefundsPage;
use Tests\Sylius\RefundPlugin\Behat\Services\Factory\FailedRefundPaymentFactory;
use Tests\Sylius\RefundPlugin\Behat\Services\Generator\FailedCreditMemoGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.behat.page.admin.order.show.class', ShowPage::class);
    $parameters->set('sylius.behat.page.shop.order.show.class', \Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order\ShowPage::class);

    $services->defaults()
        ->public();

    $services->set(OrderRefundsPage::class)
        ->parent('sylius.behat.symfony_page');

    $services->set(CreditMemoDetailsPage::class)
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')]);

    $services->set(CreditMemoIndexPage::class)
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_refund_admin_credit_memo_index']);

    $services->set(Element::class)
        ->private()
        ->abstract()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ]);

    $services->set(PdfDownloadElement::class)
        ->private()
        ->parent(Element::class);

    $services->set(RefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.repository.refund'),
            service('sylius_refund.provider.remaining_total'),
            service('sylius.command_bus'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(CreditMemoContext::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.current_date_time_immutable'),
            '%sylius_refund.credit_memo_save_path%',
        ]);

    $services->set(ManagingOrdersContext::class)
        ->args([
            service('sylius.behat.page.admin.order.show'),
            service('sylius.behat.page.admin.order.index'),
            service('sylius.behat.notification_checker.shop'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\RefundingContext::class)
        ->args([
            service(OrderRefundsPage::class),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\CreditMemoContext::class)
        ->args([
            service('sylius.behat.page.admin.order.show'),
            service(CreditMemoIndexPage::class),
            service(CreditMemoDetailsPage::class),
            service(PdfDownloadElement::class),
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.current_date_time_immutable'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_bus'),
            service(FailedCreditMemoGenerator::class),
            service(FailedRefundPaymentFactory::class),
        ]);

    $services->set(OrderContext::class)
        ->args([
            service('sylius.manager.order'),
            service('sylius.behat.shared_storage'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set(PaymentContext::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.behat.shared_storage'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Transform\OrderContext::class)
        ->args([service('sylius.repository.order')]);

    $services->set(PriceContext::class);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer\CreditMemoContext::class)
        ->args([
            service('sylius.behat.page.shop.order.show'),
            service(PdfDownloadElement::class),
        ]);

    $services->set(ChannelContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.factory.default_united_states_channel'),
            service('sylius.behat.factory.default_channel'),
            service('sylius.manager.channel'),
        ]);

    $services->set(EmailsContext::class)
        ->args([service('sylius.behat.email_checker')]);

    $services->set(ProductContext::class)
        ->args([service('sylius.behat.context.setup.product')]);

    $services->set(CreditMemosContext::class)
        ->args(['%sylius_refund.credit_memo_save_path%']);

    $services->set(FailedRefundPaymentFactory::class)
        ->private()
        ->decorate('sylius_refund.factory.refund_payment')
        ->args([service('.inner')]);

    $services->set(FailedCreditMemoGenerator::class)
        ->private()
        ->decorate('sylius_refund.generator.credit_memo')
        ->args([service('.inner')]);
};
