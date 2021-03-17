<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Psr\Log\LoggerInterface;
use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
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

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        MessageBusInterface $commandBus,
        Session $session,
        UrlGeneratorInterface $router,
        RefundUnitsCommandCreatorInterface $commandCreator,
        LoggerInterface $logger
    ) {
        $this->commandBus = $commandBus;
        $this->session = $session;
        $this->router = $router;
        $this->commandCreator = $commandCreator;
        $this->logger = $logger;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->commandBus->dispatch($this->commandCreator->fromRequest($request));

            $this->session->getFlashBag()->add('success', 'sylius_refund.units_successfully_refunded');
        } catch (InvalidRefundAmount $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());

            $this->logger->error($exception->getMessage());
        } catch (HandlerFailedException $exception) {
            /** @var \Exception $previousException */
            $previousException = $exception->getPrevious();

            $this->provideErrorMessage($previousException);

            $this->logger->error($previousException->getMessage());
        }

        return new RedirectResponse($this->router->generate(
            'sylius_refund_order_refunds_list', ['orderNumber' => $request->attributes->get('orderNumber')]
        ));
    }

    private function provideErrorMessage(\Throwable $previousException): void
    {
        if ($previousException instanceof InvalidRefundAmount) {
            $this->session->getFlashBag()->add('error', $previousException->getMessage());

            return;
        }

        $this->session->getFlashBag()->add('error', 'sylius_refund.error_occurred');
    }
}
