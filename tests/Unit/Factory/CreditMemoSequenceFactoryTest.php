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

namespace Tests\Sylius\RefundPlugin\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Entity\CreditMemoSequence;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactory;
use Sylius\RefundPlugin\Factory\CreditMemoSequenceFactoryInterface;

final class CreditMemoSequenceFactoryTest extends TestCase
{
    private CreditMemoSequenceFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CreditMemoSequenceFactory();
    }

    public function testItImplementsSequenceFactoryInterface(): void
    {
        $this->assertInstanceOf(CreditMemoSequenceFactoryInterface::class, $this->factory);
    }

    public function testItCreatesNewCreditMemoSequence(): void
    {
        $result = $this->factory->createNew();

        $this->assertEquals(new CreditMemoSequence(), $result);
    }
}