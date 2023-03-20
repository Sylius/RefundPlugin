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

namespace spec\Sylius\RefundPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;

final class RefundAmountValidatorSpec extends ObjectBehavior
{
    function let(RemainingTotalProviderInterface $remainingTotalProvider): void
    {
        $this->beConstructedWith($remainingTotalProvider);
    }

    function it_implements_refund_amount_validator_interface(): void
    {
        $this->shouldImplement(RefundAmountValidatorInterface::class);
    }

    function it_throws_exception_if_unit_refund_total_is_bigger_than_remaining_unit_refunded_total(
        RemainingTotalProviderInterface $remainingTotalProvider,
    ): void {
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);
        $refundType = RefundType::orderItemUnit();

        $remainingTotalProvider->getTotalLeftToRefund(2, $refundType)->willReturn(5);

        $this
            ->shouldThrow(InvalidRefundAmount::class)
            ->during('validateUnits', [[$correctOrderItemUnitRefund]])
        ;
    }

    function it_throws_exception_if_total_of_at_least_one_unit_is_below_zero(): void
    {
        $incorrectOrderItemUnitRefund = new OrderItemUnitRefund(1, -10);
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);

        $this
            ->shouldThrow(InvalidRefundAmount::class)
            ->during(
                'validateUnits',
                [[$incorrectOrderItemUnitRefund, $correctOrderItemUnitRefund]],
            )
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_throws_exception_if_unit_refund_total_is_bigger_than_remaining_unit_refunded_total_with_deprecations(
        RemainingTotalProviderInterface $remainingTotalProvider,
    ): void {
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);
        $refundType = RefundType::orderItemUnit();

        $remainingTotalProvider->getTotalLeftToRefund(2, $refundType)->willReturn(5);

        $this
            ->shouldThrow(InvalidRefundAmount::class)
            ->during('validateUnits', [[$correctOrderItemUnitRefund], $refundType])
        ;
    }

    /** @legacy will be removed in RefundPlugin 2.0 */
    function it_throws_exception_if_total_of_at_least_one_unit_is_below_zero_with_deprecations(): void
    {
        $incorrectOrderItemUnitRefund = new OrderItemUnitRefund(1, -10);
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);

        $this
            ->shouldThrow(InvalidRefundAmount::class)
            ->during(
                'validateUnits',
                [
                    [$incorrectOrderItemUnitRefund, $correctOrderItemUnitRefund],
                    RefundType::orderItemUnit(),
                ],
            )
        ;
    }
}
