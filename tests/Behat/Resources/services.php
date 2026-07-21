<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.behat.page.admin.order.show.class', \Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order\ShowPage::class);
    $parameters->set('sylius.behat.page.shop.order.show.class', \Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order\ShowPage::class);

    $services->defaults()
        ->public();

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\OrderRefundsPage::class)
        ->parent('sylius.behat.symfony_page');

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPage::class)
        ->parent('sylius.behat.symfony_page')
        ->args([service('sylius.behat.table_accessor')]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPage::class)
        ->parent('sylius.behat.page.admin.crud.index')
        ->args(['sylius_refund_admin_credit_memo_index']);

    $services->set(\FriendsOfBehat\PageObjectExtension\Element\Element::class)
        ->private()
        ->abstract()
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElement::class)
        ->private()
        ->parent(\FriendsOfBehat\PageObjectExtension\Element\Element::class);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Application\RefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.repository.refund'),
            service('sylius_refund.provider.remaining_total'),
            service('sylius.command_bus'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Application\CreditMemoContext::class)
        ->args([
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.current_date_time_immutable'),
            '%sylius_refund.credit_memo_save_path%',
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\ManagingOrdersContext::class)
        ->args([
            service('sylius.behat.page.admin.order.show'),
            service('sylius.behat.page.admin.order.index'),
            service('sylius.behat.notification_checker.shop'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\RefundingContext::class)
        ->args([
            service(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\OrderRefundsPage::class),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\CreditMemoContext::class)
        ->args([
            service('sylius.behat.page.admin.order.show'),
            service(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPage::class),
            service(\Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPage::class),
            service(\Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElement::class),
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.current_date_time_immutable'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_bus'),
            service(\Tests\Sylius\RefundPlugin\Behat\Services\Generator\FailedCreditMemoGenerator::class),
            service(\Tests\Sylius\RefundPlugin\Behat\Services\Factory\FailedRefundPaymentFactory::class),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\OrderContext::class)
        ->args([
            service('sylius.manager.order'),
            service('sylius.behat.shared_storage'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\PaymentContext::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius.behat.shared_storage'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Transform\OrderContext::class)
        ->args([service('sylius.repository.order')]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Transform\PriceContext::class);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer\CreditMemoContext::class)
        ->args([
            service('sylius.behat.page.shop.order.show'),
            service(\Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElement::class),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.factory.default_united_states_channel'),
            service('sylius.behat.factory.default_channel'),
            service('sylius.manager.channel'),
        ]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext::class)
        ->args([service('sylius.behat.email_checker')]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Setup\ProductContext::class)
        ->args([service('sylius.behat.context.setup.product')]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext::class)
        ->args(['%sylius_refund.credit_memo_save_path%']);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Services\Factory\FailedRefundPaymentFactory::class)
        ->private()
        ->decorate('sylius_refund.factory.refund_payment')
        ->args([service('.inner')]);

    $services->set(\Tests\Sylius\RefundPlugin\Behat\Services\Generator\FailedCreditMemoGenerator::class)
        ->private()
        ->decorate('sylius_refund.generator.credit_memo')
        ->args([service('.inner')]);
};
