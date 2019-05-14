<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\RefundPlugin\Command\SendCreditMemo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendCreditMemoAction
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(MessageBusInterface $commandBus, Session $session, UrlGeneratorInterface $router)
    {
        $this->commandBus = $commandBus;
        $this->session = $session;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        $this->commandBus->dispatch(new SendCreditMemo($request->get('id')));

        $this->session->getFlashBag()->add('success', 'sylius_refund.resend_credit_memo_success');

        return new RedirectResponse($this->router->generate('sylius_refund_admin_credit_memo_index'));
    }
}
