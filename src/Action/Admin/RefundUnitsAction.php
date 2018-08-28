<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Sylius\RefundPlugin\Creator\CommandCreatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RefundUnitsAction
{
    /** @var CommandBus */
    private $commandBus;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var CommandCreatorInterface */
    private $commandCreator;

    public function __construct(
        CommandBus $commandBus,
        Session $session,
        UrlGeneratorInterface $router,
        CommandCreatorInterface $commandCreator
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
        } catch (CommandDispatchException $exception) {
            $this->session->getFlashBag()->add('error', $exception->getPrevious()->getMessage());
        } catch (\InvalidArgumentException $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse($this->router->generate(
            'sylius_refund_order_refunds_list', ['orderNumber' => $request->attributes->get('orderNumber')]
        ));
    }
}
