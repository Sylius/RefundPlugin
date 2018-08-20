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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        Environment $twig,
        ChannelContextInterface $channelContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->twig = $twig;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($request->attributes->get('orderNumber'))) {
            return $this->redirectToReferer($request);
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/orderRefunds.html.twig', [
                'order' => $order,
                'payment_methods' => $paymentMethods
            ])
        );
    }

    private function redirectToReferer(Request $request): Response
    {
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
}
