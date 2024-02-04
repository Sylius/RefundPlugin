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

namespace spec\Sylius\RefundPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;

final class CreditMemoFileNameGeneratorSpec extends ObjectBehavior
{
    function it_implements_credit_memo_file_name_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoFileNameGeneratorInterface::class);
    }

    function it_generates_credit_memo_file_name_based_on_its_number(CreditMemoInterface $creditMemo): void
    {
        $creditMemo->getNumber()->willReturn('2018/05/000000006');

        $this->generateForPdf($creditMemo)->shouldReturn('2018_05_000000006.pdf');
    }
}
