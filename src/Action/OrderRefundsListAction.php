<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/orderRefunds.html.twig', ['order' => $order])
        );
    }
}
