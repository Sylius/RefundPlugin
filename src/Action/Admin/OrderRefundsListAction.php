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

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Provider\RefundPaymentMethodsProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final readonly class OrderRefundsListAction
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderRefundingAvailabilityCheckerInterface $orderRefundsListAvailabilityChecker,
        private RefundPaymentMethodsProviderInterface $refundPaymentMethodsProvider,
        private Environment $twig,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $router,
        private ?StateMachineInterface $stateMachine = null,
    ) {
        if (null === $this->stateMachine) {
            trigger_deprecation(
                'sylius/refund-plugin',
                '2.0',
                'Not passing an $stateMachine to %s constructor is deprecated and will be prohibited in SyliusRefund 3.0.',
                self::class,
            );
        }
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        if (null !== $this->stateMachine) {
            if ($order->getPaymentState() !== OrderPaymentStates::STATE_REFUNDED &&
                false === $this->stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND) &&
                false === $this->stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)
            ) {
                throw new AccessDeniedHttpException('This order cannot be refunded.');
            }
        }

        if (!$this->orderRefundsListAvailabilityChecker->__invoke($request->attributes->get('orderNumber'))) {
            if ($order->getTotal() === 0) {
                return $this->redirectToReferer($order, 'sylius_refund.free_order_should_not_be_refund');
            }

            return $this->redirectToReferer($order, 'sylius_refund.order_should_be_paid');
        }

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/order_refunds.html.twig', [
                'order' => $order,
                'payment_methods' => $this->refundPaymentMethodsProvider->findForOrder($order),
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
        /** @var FlashBagInterface $flashBag */
        $flashBag = $this->requestStack->getSession()->getBag('flashes');

        return $flashBag;
    }
}
