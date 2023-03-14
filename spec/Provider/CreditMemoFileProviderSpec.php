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

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;

final class CreditMemoFileProviderSpec extends ObjectBehavior
{
    function let(
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
    ): void {
        $this->beConstructedWith($creditMemoFileNameGenerator, $creditMemoFileManager);
    }

    function it_implements_credit_memo_file_provider_interface(): void
    {
        $this->shouldImplement(CreditMemoFileProviderInterface::class);
    }

    function it_provides_credit_memo_pdf_for_credit_memo(
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoFileNameGenerator->generateForPdf($creditMemo)->willReturn('credit_memo.pdf');

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoFileManager->get('credit_memo.pdf')->willReturn($creditMemoPdf);

        $this->provide($creditMemo)->shouldBeLike($creditMemoPdf);
    }
}
