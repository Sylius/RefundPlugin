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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Symfony\Component\HttpFoundation\Request;

final class ResendCreditMemoAction
{
    /** @var RepositoryInterface  */
    private $creditMemoRepository;

    /** @var CreditMemoEmailSenderInterface */
    private $creditMemoEmailSender;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        CreditMemoEmailSenderInterface $creditMemoEmailSender,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->creditMemoEmailSender = $creditMemoEmailSender;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request, string $id): void
    {
        /** @var CreditMemoInterface $creditMemo */
        $creditMemo = $this->creditMemoRepository->findOneBy(['id' => $id]);

        $orderNumber = $creditMemo->getOrderNumber();

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        /** @var string $recipient */
        $recipient = $order->getCustomer()->getEmail();

        $this->creditMemoEmailSender->send($creditMemo, $recipient);
    }
}
