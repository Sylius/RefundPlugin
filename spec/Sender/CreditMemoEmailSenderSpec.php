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

namespace spec\Sylius\RefundPlugin\Sender;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;

final class CreditMemoEmailSenderSpec extends ObjectBehavior
{
    function let(
        SenderInterface $sender,
        CreditMemoFileResolverInterface $creditMemoFileResolver,
        CreditMemoFilePathResolverInterface $creditMemoFilePathResolver,
    ): void {
        $this->beConstructedWith(
            $sender,
            true,
            $creditMemoFileResolver,
            $creditMemoFilePathResolver,
        );
    }

    function it_implements_credit_memo_email_sender_interface(): void
    {
        $this->shouldImplement(CreditMemoEmailSenderInterface::class);
    }

    function it_sends_an_email_with_credit_memo_and_pdf_file_attachment_to_customer(
        SenderInterface $sender,
        CreditMemoFileResolverInterface $creditMemoFileResolver,
        CreditMemoFilePathResolverInterface $creditMemoFilePathResolver,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'I am credit memo number #2018/10/000444');
        $creditMemoFileResolver->resolveByCreditMemo($creditMemo)->willReturn($creditMemoPdf);
        $creditMemoFilePathResolver->resolve($creditMemoPdf)->willReturn('/path/to/credit_memos/credit_memo.pdf');

        $sender
            ->send('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo], ['/path/to/credit_memos/credit_memo.pdf'])
            ->shouldBeCalled()
        ;

        $this->send($creditMemo, 'john@example.com');
    }

    function it_sends_an_email_with_credit_memo_to_customer_without_pdf_file_attachment_if_pdf_file_generator_is_disabled(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        SenderInterface $sender,
        CreditMemoFileResolverInterface $creditMemoFileResolver,
        CreditMemoFilePathResolverInterface $creditMemoFilePathResolver,
        CreditMemoInterface $creditMemo,
    ): void {
        $this->beConstructedWith($sender, false, $creditMemoFileResolver, $creditMemoFilePathResolver);

        $creditMemoPdfFileGenerator->generate(Argument::any())->shouldNotBeCalled();
        $creditMemoFileResolver->resolveByCreditMemo(Argument::any())->shouldNotBeCalled();
        $creditMemoFilePathResolver->resolve(Argument::any())->shouldNotBeCalled();

        $sender->send('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo])->shouldBeCalled();

        $this->send($creditMemo, 'john@example.com');
    }
}
