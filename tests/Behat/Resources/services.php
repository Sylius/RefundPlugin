<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FriendsOfBehat\PageObjectExtension\Element\Element;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\CreditMemoContext as ApplicationCreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\EmailsContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Application\RefundingContext as ApplicationRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Hook\CreditMemosContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ChannelContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\OrderContext as SetupOrderContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\PaymentContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\ProductContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Setup\RefundingContext as SetupRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Transform\OrderContext as TransformOrderContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Transform\PriceContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\CreditMemoContext as UiCreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\ManagingOrdersContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\RefundingContext as UiRefundingContext;
use Tests\Sylius\RefundPlugin\Behat\Context\Ui\Shop\Customer\CreditMemoContext as ShopCustomerCreditMemoContext;
use Tests\Sylius\RefundPlugin\Behat\Element\PdfDownloadElement;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoDetailsPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\CreditMemoIndexPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\Order\ShowPage as AdminOrderShowPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Admin\OrderRefundsPage;
use Tests\Sylius\RefundPlugin\Behat\Page\Shop\Order\ShowPage as ShopOrderShowPage;
use Tests\Sylius\RefundPlugin\Behat\Services\Factory\FailedRefundPaymentFactory;
use Tests\Sylius\RefundPlugin\Behat\Services\Generator\FailedCreditMemoGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.behat.page.admin.order.show.class', AdminOrderShowPage::class);
    $parameters->set('sylius.behat.page.shop.order.show.class', ShopOrderShowPage::class);

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

    $services->set(ApplicationRefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_refund.repository.refund'),
            service('sylius_refund.provider.remaining_total'),
            service('sylius.command_bus'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(ApplicationCreditMemoContext::class)
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

    $services->set(UiRefundingContext::class)
        ->args([
            service(OrderRefundsPage::class),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.email_checker'),
        ]);

    $services->set(UiCreditMemoContext::class)
        ->args([
            service('sylius.behat.page.admin.order.show'),
            service(CreditMemoIndexPage::class),
            service(CreditMemoDetailsPage::class),
            service(PdfDownloadElement::class),
            service('sylius_refund.repository.credit_memo'),
            service('sylius_refund.provider.current_date_time_immutable'),
        ]);

    $services->set(SetupRefundingContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_bus'),
            service(FailedCreditMemoGenerator::class),
            service(FailedRefundPaymentFactory::class),
        ]);

    $services->set(SetupOrderContext::class)
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

    $services->set(TransformOrderContext::class)
        ->args([service('sylius.repository.order')]);

    $services->set(PriceContext::class);

    $services->set(ShopCustomerCreditMemoContext::class)
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
