<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Validator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Sylius\RefundPlugin\Validator\RefundAmountValidator;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;

final class RefundAmountValidatorTest extends TestCase
{
    private RemainingTotalProviderInterface&MockObject $remainingTotalProvider;

    private RefundAmountValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->remainingTotalProvider = $this->createMock(RemainingTotalProviderInterface::class);
        $this->validator = new RefundAmountValidator($this->remainingTotalProvider);
    }

    #[Test]
    public function it_implements_refund_amount_validator_interface(): void
    {
        self::assertInstanceOf(RefundAmountValidatorInterface::class, $this->validator);
    }

    #[Test]
    public function it_throws_exception_if_unit_refund_total_is_bigger_than_remaining_unit_refunded_total(): void
    {
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);
        $refundType = RefundType::orderItemUnit();

        $this->remainingTotalProvider
            ->expects(self::once())
            ->method('getTotalLeftToRefund')
            ->with(2, $refundType)
            ->willReturn(5);

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validateUnits([$correctOrderItemUnitRefund]);
    }

    #[Test]
    public function it_throws_exception_if_total_of_at_least_one_unit_is_below_zero(): void
    {
        $incorrectOrderItemUnitRefund = new OrderItemUnitRefund(1, -10);
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validateUnits([$incorrectOrderItemUnitRefund, $correctOrderItemUnitRefund]);
    }

    /**
     * @legacy will be removed in RefundPlugin 2.0
     */
    #[Test]
    public function it_throws_exception_if_unit_refund_total_is_bigger_than_remaining_unit_refunded_total_with_deprecations(): void
    {
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);
        $refundType = RefundType::orderItemUnit();

        $this->remainingTotalProvider
            ->expects(self::once())
            ->method('getTotalLeftToRefund')
            ->with(2, $refundType)
            ->willReturn(5);

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validateUnits([$correctOrderItemUnitRefund]);
    }

    /**
     * @legacy will be removed in RefundPlugin 2.0
     */
    #[Test]
    public function it_throws_exception_if_total_of_at_least_one_unit_is_below_zero_with_deprecations(): void
    {
        $incorrectOrderItemUnitRefund = new OrderItemUnitRefund(1, -10);
        $correctOrderItemUnitRefund = new OrderItemUnitRefund(2, 10);

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validateUnits(
            [$incorrectOrderItemUnitRefund, $correctOrderItemUnitRefund],
        );
    }
}
