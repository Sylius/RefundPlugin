<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\Component\Resource\Repository\RepositoryInterface;
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

    /** @var RepositoryInterface */
    private $creditMemoRepository;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        MessageBusInterface $commandBus,
        RepositoryInterface $creditMemoRepository,
        Session $session,
        UrlGeneratorInterface $router
    ) {
        $this->commandBus = $commandBus;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->session = $session;
        $this->router = $router;
    }


    public function __invoke(Request $request): Response
    {
        $creditMemoNumber = $this->creditMemoRepository->find($request->get('id'))->getNumber();

        $this->commandBus->dispatch(new SendCreditMemo($creditMemoNumber));

        $this->session->getFlashBag()->add('success', 'sylius_refund.resend_credit_memo_success');

        return new RedirectResponse($this->router->generate('sylius_refund_admin_credit_memo_index'));
    }
}
