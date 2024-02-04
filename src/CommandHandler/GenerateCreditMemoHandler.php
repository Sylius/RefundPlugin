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

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class GenerateCreditMemoHandler
{
    public function __construct(
        private CreditMemoGeneratorInterface $creditMemoGenerator,
        private ObjectManager $creditMemoManager,
        private MessageBusInterface $eventBus,
        private OrderRepositoryInterface $orderRepository,
        private bool $hasEnabledPdfFileGenerator,
        private CreditMemoFileResolverInterface $creditMemoFileResolver,
    ) {
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $creditMemo = $this->creditMemoGenerator->generate(
            $order,
            $command->total(),
            $command->units(),
            $command->comment(),
        );

        $this->creditMemoManager->persist($creditMemo);
        $this->creditMemoManager->flush();

        $this->generatePdf($creditMemo);

        $number = $creditMemo->getNumber();
        Assert::notNull($number);

        $this->eventBus->dispatch(new CreditMemoGenerated($number, $orderNumber));
    }

    private function generatePdf(CreditMemoInterface $creditMemo): void
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            return;
        }

        $this->creditMemoFileResolver->resolveByCreditMemo($creditMemo);
    }
}
