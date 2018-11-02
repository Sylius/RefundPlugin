<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RefundUnitsAction
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var RefundUnitsCommandCreatorInterface */
    private $commandCreator;

    public function __construct(
        MessageBusInterface $commandBus,
        Session $session,
        UrlGeneratorInterface $router,
        RefundUnitsCommandCreatorInterface $commandCreator
    ) {
        $this->commandBus = $commandBus;
        $this->session = $session;
        $this->router = $router;
        $this->commandCreator = $commandCreator;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->commandBus->dispatch($this->commandCreator->fromRequest($request));

            $this->session->getFlashBag()->add('success', 'sylius_refund.units_successfully_refunded');
        } catch (\InvalidArgumentException | OrderNotFound $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse($this->router->generate(
            'sylius_refund_order_refunds_list', ['orderNumber' => $request->attributes->get('orderNumber')]
        ));
    }
}
