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
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class RefundUnitsHandler
{
    private ?RefunderInterface $orderUnitsRefunder = null;

    private ?RefunderInterface $orderShipmentsRefunder = null;

    public function __construct(
        private iterable|RefunderInterface $refunders,
        private MessageBusInterface|RefunderInterface $eventBus,
        private OrderRepositoryInterface|MessageBusInterface $orderRepository,
        private RefundUnitsCommandValidatorInterface|OrderRepositoryInterface $refundUnitsCommandValidator,
    ) {
        $args = func_get_args();

        if ($refunders instanceof RefunderInterface) {
            if (!isset($args[4])) {
                throw new \InvalidArgumentException('The 5th argument must be present.');
            }

            $this->orderUnitsRefunder = $refunders;
            /** @phpstan-ignore-next-line */
            $this->orderShipmentsRefunder = $this->eventBus;
            /** @phpstan-ignore-next-line */
            $this->eventBus = $orderRepository;
            /** @phpstan-ignore-next-line */
            $this->orderRepository = $refundUnitsCommandValidator;
            $this->refundUnitsCommandValidator = $args[4];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing a "%s" as a 1st argument of "%s" constructor is deprecated and will be removed in 2.0.', RefunderInterface::class, self::class));
        }
    }

    public function __invoke(RefundUnits $command): void
    {
        Assert::isInstanceOf($this->refundUnitsCommandValidator, RefundUnitsCommandValidatorInterface::class);
        Assert::isInstanceOf($this->orderRepository, OrderRepositoryInterface::class);
        Assert::isInstanceOf($this->eventBus, MessageBusInterface::class);

        $this->refundUnitsCommandValidator->validate($command);

        $orderNumber = $command->orderNumber();

        $refundedTotal = 0;

        $units = array_merge($command->units(), $command->shipments());

        if (null !== $this->orderUnitsRefunder && null !== $this->orderShipmentsRefunder) {
            $refundedTotal += $this->orderUnitsRefunder->refundFromOrder(array_values(array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof OrderItemUnitRefund)), $orderNumber);
            $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder(array_values(array_filter($units, fn (UnitRefundInterface $unitRefund) => $unitRefund instanceof ShipmentRefund)), $orderNumber);
        } else {
            Assert::isIterable($this->refunders);

            foreach ($this->refunders as $refunder) {
                Assert::isInstanceOf($refunder, RefunderInterface::class);

                $refundedTotal += $refunder->refundFromOrder($units, $orderNumber);
            }
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
