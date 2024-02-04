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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class RefundUnitsHandler
{
    public function __construct(
        /** @var iterable<RefunderInterface> $refunders */
        private iterable $refunders,
        private MessageBusInterface $eventBus,
        private OrderRepositoryInterface $orderRepository,
        private RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator,
    ) {
    }

    public function __invoke(RefundUnits $command): void
    {
        $this->refundUnitsCommandValidator->validate($command);

        $orderNumber = $command->orderNumber();

        $refundedTotal = 0;

        $units = $command->units();

        foreach ($this->refunders as $refunder) {
            $refundedTotal += $refunder->refundFromOrder($units, $orderNumber);
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        /** @var string|null $currencyCode */
        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);

        $this->eventBus->dispatch(new UnitsRefunded(
            $orderNumber,
            $units,
            $command->paymentMethodId(),
            $refundedTotal,
            $currencyCode,
            $command->comment(),
        ));
    }
}
