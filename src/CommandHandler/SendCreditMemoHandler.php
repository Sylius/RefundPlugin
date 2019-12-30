<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Command\SendCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Webmozart\Assert\Assert;

final class SendCreditMemoHandler
{
    /** @var RepositoryInterface */
    private $creditMemoRepository;

    /** @var CreditMemoEmailSenderInterface */
    private $creditMemoEmailSender;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        CreditMemoEmailSenderInterface $creditMemoEmailSender
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->creditMemoEmailSender = $creditMemoEmailSender;
    }

    public function __invoke(SendCreditMemo $command): void
    {
        $creditMemoNumber = $command->number();

        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->findOneBy(['number' => $creditMemoNumber]);
        if ($creditMemo === null) {
            throw CreditMemoNotFound::withNumber($creditMemoNumber);
        }

        $order = $creditMemo->getOrder();

        Assert::notNull($order->getCustomer(), 'Credit memo order has no customer');

        $this->creditMemoEmailSender->send($creditMemo, $order->getCustomer()->getEmail());
    }
}
