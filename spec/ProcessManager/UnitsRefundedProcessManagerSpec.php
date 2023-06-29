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

namespace spec\Sylius\RefundPlugin\ProcessManager;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessManagerInterface;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;

final class UnitsRefundedProcessManagerSpec extends ObjectBehavior
{
    function let(
        UnitsRefundedProcessStepInterface $creditMemoProcessManager,
        UnitsRefundedProcessStepInterface $refundPaymentProcessManager,
    ): void {
        $this->beConstructedWith([$creditMemoProcessManager, $refundPaymentProcessManager]);
    }

    function it_implements_units_refunded_process_manager_interface(): void
    {
        $this->shouldImplement(UnitsRefundedProcessManagerInterface::class);
    }

    function it_triggers_all_process_steps_if_all_are_successful(
        UnitsRefundedProcessStepInterface $creditMemoProcessManager,
        UnitsRefundedProcessStepInterface $refundPaymentProcessManager,
    ): void {
        $unitRefunds = [
            new OrderItemUnitRefund(1, 1000),
            new OrderItemUnitRefund(3, 2000),
            new OrderItemUnitRefund(5, 3000),
            new ShipmentRefund(1, 500),
            new ShipmentRefund(2, 1000),
        ];
        $event = new UnitsRefunded('000222', $unitRefunds, 1, 1500, 'USD', 'Comment');

        $creditMemoProcessManager->next($event)->shouldBeCalled();
        $refundPaymentProcessManager->next($event)->shouldBeCalled();

        $this($event);
    }
}
