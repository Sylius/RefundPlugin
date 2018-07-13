<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

final class OrderRefundsListAction
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var Environment */
    private $twig;

    public function __construct(OrderRepositoryInterface $orderRepository, Environment $twig)
    {
        $this->orderRepository = $orderRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        if ($order->getPaymentState() !== OrderPaymentStates::STATE_PAID) {
            /** @var SessionInterface|null $session */
            $session = $request->getSession();
            if (null !== $session) {
                /** @var FlashBagInterface $flashBag */
                $flashBag = $session->getBag('flashes');
                $flashBag->add('error', 'sylius_refund.order_should_be_paid');
            }

            /** @var string $referer */
            $referer = $request->headers->get('referer');

            return new RedirectResponse($referer);
        }

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/orderRefunds.html.twig', ['order' => $order])
        );
    }
}
