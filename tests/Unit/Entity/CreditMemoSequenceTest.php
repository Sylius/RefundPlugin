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

namespace Tests\Sylius\RefundPlugin\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Entity\CreditMemoSequenceInterface;

final class CreditMemoSequenceTest extends TestCase
{
    private CreditMemoSequence $creditMemoSequence;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemoSequence = new CreditMemoSequence();
    }

    /** @test */
    function it_implements_credit_memo_sequence_interface(): void
    {
        self::assertInstanceOf(CreditMemoSequenceInterface::class, $this->creditMemoSequence);
    }

    /** @test */
    function it_has_no_id_by_default(): void
    {
        self::assertNull($this->creditMemoSequence->getId());
    }

    /** @test */
    function it_has_incrementable_index(): void
    {
        self::assertEquals(0, $this->creditMemoSequence->getIndex());

        $this->creditMemoSequence->incrementIndex();
        $this->creditMemoSequence->incrementIndex();

        self::assertEquals(2, $this->creditMemoSequence->getIndex());
    }

    /** @test */
    function it_has_version(): void
    {
        self::assertEquals(1, $this->creditMemoSequence->getVersion());

        $this->creditMemoSequence->setVersion(2);
        self::assertEquals(2, $this->creditMemoSequence->getVersion());
    }
}
