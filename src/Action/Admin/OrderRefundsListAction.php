<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class OrderRefundsListAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderRefundingAvailabilityCheckerInterface $orderRefundsListAvailabilityChecker,
        private RefundPaymentMethodsProviderInterface $refundPaymentMethodsProvider,
        private Environment $twig,
        private SessionInterface | RequestStack $requestStackOrSession,
        private UrlGeneratorInterface $router,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation('sylius/refund-plugin', '1.3', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius Refund Plugin 1.3 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
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
            ]),
        );
    }

    private function redirectToReferer(OrderInterface $order, string $message): Response
    {
        $this->getFlashBag()->add('error', $message);

        return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $order->getId()]));
    }

    private function getFlashBag(): FlashBagInterface
    {
        if ($this->requestStackOrSession instanceof RequestStack) {
            return $this->requestStackOrSession->getSession()->getBag('flashes');
        }

        return $this->requestStackOrSession->getBag('flashes');
    }
}
