<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class OrderRefundsListAction
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundsListAvailabilityChecker;

    /** @var RefundPaymentMethodsProviderInterface */
    private $refundPaymentMethodsProvider;

    /** @var Environment */
    private $twig;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderRefundingAvailabilityCheckerInterface $orderRefundsListAvailabilityChecker,
        RefundPaymentMethodsProviderInterface $refundPaymentMethodsProvider,
        Environment $twig,
        Session $session,
        UrlGeneratorInterface $router
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderRefundsListAvailabilityChecker = $orderRefundsListAvailabilityChecker;
        $this->refundPaymentMethodsProvider = $refundPaymentMethodsProvider;
        $this->twig = $twig;
        $this->session = $session;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        if (!$this->orderRefundsListAvailabilityChecker->__invoke($request->attributes->get('orderNumber'))) {
            if ($order->getTotal() === 0) {
                return $this->redirectToReferer($order, 'sylius_refund.free_order_should_not_be_refund');
            }

            return $this->redirectToReferer($order, 'sylius_refund.order_should_be_paid');
        }

        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel);

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/orderRefunds.html.twig', [
                'order' => $order,
                'payment_methods' => $this->refundPaymentMethodsProvider->findForChannel($channel),
            ])
        );
    }

    private function redirectToReferer(OrderInterface $order, string $message): Response
    {
        $this->session->getFlashBag()->add('error', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $order->getId()]));
    }
}
