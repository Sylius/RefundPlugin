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

namespace Tests\Sylius\RefundPlugin\Unit\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationChecker;
use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Exception\CreditMemoNotAccessible;

final class CreditMemoCustomerRelationCheckerTest extends TestCase
{
    private CustomerContextInterface&MockObject $customerContext;

    private RepositoryInterface&MockObject $creditMemoRepository;

    private CreditMemoCustomerRelationChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->creditMemoRepository = $this->createMock(RepositoryInterface::class);
        $this->checker = new CreditMemoCustomerRelationChecker($this->customerContext, $this->creditMemoRepository);
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(CreditMemoCustomerRelationChecker::class, $this->checker);
    }

    /** @test */
    public function it_implements_credit_memo_customer_relation_checker_interface(): void
    {
        self::assertInstanceOf(CreditMemoCustomerRelationCheckerInterface::class, $this->checker);
    }

    /** @test */
    public function it_checks_if_customer_id_from_order_is_equal_to_customer_id_from_customer_context(): void
    {
        $creditMemo = $this->createMock(CreditMemo::class);
        $order = $this->createMock(OrderInterface::class);
        $orderCustomer = $this->createMock(CustomerInterface::class);
        $contextCustomer = $this->createMock(CustomerInterface::class);

        $this->creditMemoRepository
            ->expects(self::once())
            ->method('find')
            ->with('00001')
            ->willReturn($creditMemo);

        $creditMemo
            ->expects(self::once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($orderCustomer);

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($contextCustomer);

        $orderCustomer
            ->expects(self::once())
            ->method('getId')
            ->willReturn(1);

        $contextCustomer
            ->expects(self::once())
            ->method('getId')
            ->willReturn(1);

        $this->checker->check('00001');
    }

    /** @test */
    public function it_throws_exception_if_customer_id_from_order_is_not_equal_to_id_from_context(): void
    {
        $creditMemo = $this->createMock(CreditMemo::class);
        $order = $this->createMock(OrderInterface::class);
        $firstCustomer = $this->createMock(CustomerInterface::class);
        $secondCustomer = $this->createMock(CustomerInterface::class);

        $this->creditMemoRepository
            ->expects(self::once())
            ->method('find')
            ->with('00001')
            ->willReturn($creditMemo);

        $creditMemo
            ->expects(self::once())
            ->method('getOrder')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($firstCustomer);

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($secondCustomer);

        $firstCustomer
            ->expects(self::once())
            ->method('getId')
            ->willReturn(1);

        $secondCustomer
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(2);

        $this->expectException(CreditMemoNotAccessible::class);
        $this->checker->check('00001');
    }
}
