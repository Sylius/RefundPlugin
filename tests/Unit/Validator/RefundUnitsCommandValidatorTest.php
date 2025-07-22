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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefunding;
use Sylius\RefundPlugin\Exception\RefundUnitsNotBelongToOrder;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidator;
use Sylius\RefundPlugin\Validator\UnitRefundsBelongingToOrderValidatorInterface;

final class RefundUnitsCommandValidatorTest extends TestCase
{
    private OrderRefundingAvailabilityCheckerInterface&MockObject $orderRefundingAvailabilityChecker;

    private RefundAmountValidatorInterface&MockObject $refundAmountValidator;

    private UnitRefundsBelongingToOrderValidatorInterface&MockObject $firstUnitRefundsBelongingToOrderValidator;

    private UnitRefundsBelongingToOrderValidatorInterface&MockObject $secondUnitRefundsBelongingToOrderValidator;

    private RefundUnitsCommandValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRefundingAvailabilityChecker = $this->createMock(OrderRefundingAvailabilityCheckerInterface::class);
        $this->refundAmountValidator = $this->createMock(RefundAmountValidatorInterface::class);
        $this->firstUnitRefundsBelongingToOrderValidator = $this->createMock(UnitRefundsBelongingToOrderValidatorInterface::class);
        $this->secondUnitRefundsBelongingToOrderValidator = $this->createMock(UnitRefundsBelongingToOrderValidatorInterface::class);

        $this->validator = new RefundUnitsCommandValidator(
            $this->orderRefundingAvailabilityChecker,
            $this->refundAmountValidator,
            [
                $this->firstUnitRefundsBelongingToOrderValidator,
                $this->secondUnitRefundsBelongingToOrderValidator,
            ],
        );
    }

    /** @test */
    public function it_throws_exception_when_order_is_not_available_for_refund(): void
    {
        $this->orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('000001')
            ->willReturn(false);

        $refundUnits = new RefundUnits('000001', [], 1, '');

        $this->expectException(OrderNotAvailableForRefunding::class);

        $this->validator->validate($refundUnits);
    }

    /** @test */
    public function it_throws_exception_when_order_item_units_amount_is_not_valid(): void
    {
        $this->orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('000001')
            ->willReturn(true);

        $orderItemUnitRefund = new OrderItemUnitRefund(1, 10);
        $refundUnits = new RefundUnits('000001', [$orderItemUnitRefund], 1, '');

        $this->refundAmountValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$orderItemUnitRefund])
            ->willThrowException(new InvalidRefundAmount());

        $this->firstUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$orderItemUnitRefund], '000001');

        $this->secondUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$orderItemUnitRefund], '000001');

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validate($refundUnits);
    }

    /** @test */
    public function it_throws_exception_when_order_item_units_do_not_belong_to_an_order(): void
    {
        $this->orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('000001')
            ->willReturn(true);

        $orderItemUnitRefund = new OrderItemUnitRefund(1, 10);
        $refundUnits = new RefundUnits('000001', [$orderItemUnitRefund], 1, '');

        $this->refundAmountValidator
            ->expects($this->never())
            ->method('validateUnits');

        $this->firstUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$orderItemUnitRefund], '000001')
            ->willThrowException(new RefundUnitsNotBelongToOrder());

        $this->secondUnitRefundsBelongingToOrderValidator
            ->expects($this->never())
            ->method('validateUnits');

        $this->expectException(RefundUnitsNotBelongToOrder::class);

        $this->validator->validate($refundUnits);
    }

    /** @test */
    public function it_throws_exception_when_shipment_amount_is_not_valid(): void
    {
        $this->orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('000001')
            ->willReturn(true);

        $shipmentRefund = new ShipmentRefund(1, 10);
        $refundUnits = new RefundUnits('000001', [$shipmentRefund], 1, '');

        $this->refundAmountValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$shipmentRefund])
            ->willThrowException(new InvalidRefundAmount());

        $this->firstUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$shipmentRefund], '000001');

        $this->secondUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$shipmentRefund], '000001');

        $this->expectException(InvalidRefundAmount::class);

        $this->validator->validate($refundUnits);
    }

    /** @test */
    public function it_throws_exception_when_shipment_does_not_belong_to_an_order(): void
    {
        $this->orderRefundingAvailabilityChecker
            ->expects(self::once())
            ->method('__invoke')
            ->with('000001')
            ->willReturn(true);

        $shipmentRefund = new ShipmentRefund(1, 10);
        $refundUnits = new RefundUnits('000001', [$shipmentRefund], 1, '');

        $this->refundAmountValidator
            ->expects($this->never())
            ->method('validateUnits');

        $this->firstUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$shipmentRefund], '000001');

        $this->secondUnitRefundsBelongingToOrderValidator
            ->expects(self::once())
            ->method('validateUnits')
            ->with([$shipmentRefund], '000001')
            ->willThrowException(new RefundUnitsNotBelongToOrder());

        $this->expectException(RefundUnitsNotBelongToOrder::class);

        $this->validator->validate($refundUnits);
    }
}
