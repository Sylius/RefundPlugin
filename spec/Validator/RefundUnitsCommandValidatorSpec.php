<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\CreditMemoCommentTooLongException;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;

final class RefundUnitsCommandValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $this->beConstructedWith($orderRefundingAvailabilityChecker);
    }

    function it_throws_exception_if_order_cannot_be_refunded(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('00001111')->willReturn(false);

        $this
            ->shouldThrow(OrderNotAvailableForRefundingException::withOrderNumber('00001111'))
            ->during('validate', [new RefundUnits('00001111', [], [], 1, 'Comment')])
        ;
    }

    function it_throws_exception_if_credit_memo_comment_is_too_long(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('00001111')->willReturn(true);

        $this
            ->shouldThrow(CreditMemoCommentTooLongException::class)
            ->during('validate', [new RefundUnits('00001111', [], [], 1, $this->getCorrectString().'123')])
        ;
    }

    function it_does_nothing_if_command_is_valid(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker
    ): void {
        $orderRefundingAvailabilityChecker->__invoke('00001111')->willReturn(true);

        $this
            ->shouldNotThrow(\Exception::class)
            ->during('validate', [new RefundUnits('00001111', [], [], 1, $this->getCorrectString())])
        ;
    }

    private function getCorrectString(): string
    {
        return 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis';
    }
}
