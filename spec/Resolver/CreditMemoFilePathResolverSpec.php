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

namespace spec\Sylius\RefundPlugin\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;

final class CreditMemoFilePathResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/path/to/credit_memos');
    }

    function it_implements_credit_memo_file_path_resolver_interface(): void
    {
        $this->shouldImplement(CreditMemoFilePathResolverInterface::class);
    }

    function it_resolves_credit_memo_pdf_file_path(): void
    {
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');

        $this->resolve($creditMemoPdf)->shouldReturn('/path/to/credit_memos/credit_memo.pdf');
    }
}
