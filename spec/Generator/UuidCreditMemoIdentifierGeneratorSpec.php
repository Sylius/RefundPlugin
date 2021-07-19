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

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\UuidCreditMemoIdentifierGenerator;

final class UuidCreditMemoIdentifierGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(UuidCreditMemoIdentifierGenerator::class);
    }

    function it_implements_credit_memo_identifier_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoIdentifierGeneratorInterface::class);
    }

    function it_returns_two_different_strings_on_subsequent_calls(): void
    {
        $this->generate()->shouldNotReturn($this->generate());
    }
}
