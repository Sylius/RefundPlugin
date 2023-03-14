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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class SendCreditMemoAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RepositoryInterface $creditMemoRepository,
        private SessionInterface | RequestStack $requestStackOrSession,
        private UrlGeneratorInterface $router,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation('sylius/refund-plugin', '1.3', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius Refund Plugin 1.3 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
    }

    public function __invoke(Request $request): Response
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($request->get('id'));

        if ($creditMemo !== null) {
            $number = $creditMemo->getNumber();
            Assert::notNull($number);

            $this->commandBus->dispatch(new SendCreditMemo($number));

            return $this->addFlashAndRedirect('success', 'sylius_refund.resend_credit_memo_success');
        }

        return $this->addFlashAndRedirect('failed', 'sylius_refund.resend_credit_memo_failed');
    }

    public function addFlashAndRedirect(string $flashType, string $message): RedirectResponse
    {
        $this->getFlashBag()->add($flashType, $message);

        return new RedirectResponse($this->router->generate('sylius_refund_admin_credit_memo_index'));
    }

    private function getFlashBag(): FlashBagInterface
    {
        if ($this->requestStackOrSession instanceof RequestStack) {
            return $this->requestStackOrSession->getSession()->getBag('flashes');
        }

        return $this->requestStackOrSession->getBag('flashes');
    }
}
