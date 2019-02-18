<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoGeneratedEventListener
{
    /** @var RepositoryInterface */
    private $creditMemoRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CreditMemoEmailSenderInterface */
    private $creditMemoEmailSender;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        CreditMemoEmailSenderInterface $creditMemoEmailSender
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->orderRepository = $orderRepository;
        $this->creditMemoEmailSender = $creditMemoEmailSender;
    }

    public function __invoke(CreditMemoGenerated $event): void
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->findOneBy(['number' => $event->number()]);
        if ($creditMemo === null) {
            throw CreditMemoNotFound::withNumber($event->number());
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($event->orderNumber());
        if ($order === null) {
            throw OrderNotFound::withNumber($event->orderNumber());
        }

        Assert::notNull($order->getCustomer(), 'Credit memo order has no customer');

        $this->creditMemoEmailSender->send($creditMemo, $order->getCustomer()->getEmail());
    }
}
