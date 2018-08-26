<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;

final class GenerateCreditMemoHandler
{
    /** @var CreditMemoGeneratorInterface */
    private $creditMemoGenerator;

    /** @var ObjectManager */
    private $creditMemoManager;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        EventBus $eventBus
    ) {
        $this->creditMemoGenerator = $creditMemoGenerator;
        $this->creditMemoManager = $creditMemoManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();

        $creditMemo = $this->creditMemoGenerator->generate(
            $orderNumber,
            $command->total(),
            $command->units(),
            $command->shipmentIds(),
            $command->comment()
        );

        $this->creditMemoManager->persist($creditMemo);
        $this->creditMemoManager->flush();

        $this->eventBus->dispatch(new CreditMemoGenerated($creditMemo->getNumber(), $orderNumber));
    }
}
