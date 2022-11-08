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
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class RefundUnitsAction
{
    private MessageBusInterface $commandBus;

    private Session $session;

    private UrlGeneratorInterface $router;

    private RefundUnitsCommandCreatorInterface $commandCreator;

    private LoggerInterface $logger;

    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        MessageBusInterface $commandBus,
        Session $session,
        UrlGeneratorInterface $router,
        RefundUnitsCommandCreatorInterface $commandCreator,
        LoggerInterface $logger,
        CsrfTokenManagerInterface $csrfTokenManager,
    ) {
        $this->commandBus = $commandBus;
        $this->session = $session;
        $this->router = $router;
        $this->commandCreator = $commandCreator;
        $this->logger = $logger;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function __invoke(Request $request): Response
    {
        $token = new CsrfToken(
            (string) $request->attributes->get('orderNumber'),
            (string) $request->request->get('_csrf_token')
        );
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            return new Response('Invalid CSRF token.', Response::HTTP_FORBIDDEN);
        }

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
            'sylius_refund_order_refunds_list',
            ['orderNumber' => $request->attributes->get('orderNumber')]
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
