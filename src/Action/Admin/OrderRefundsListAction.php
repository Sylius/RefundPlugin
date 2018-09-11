<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class OrderRefundsListAction
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var Environment */
    private $twig;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        Environment $twig,
        ChannelContextInterface $channelContext,
        Session $session,
        UrlGeneratorInterface $router
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->twig = $twig;
        $this->channelContext = $channelContext;
        $this->session = $session;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        if (!$this->orderRefundingAvailabilityChecker->__invoke($request->attributes->get('orderNumber'))) {
            return $this->redirectToReferer($order);
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/orderRefunds.html.twig', [
                'order' => $order,
                'payment_methods' => $paymentMethods,
            ])
        );
    }

    private function redirectToReferer(OrderInterface $order): Response
    {
        $this->session->getFlashBag()->add('error', 'sylius_refund.order_should_be_paid');

        return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $order->getId()]));
    }
}
