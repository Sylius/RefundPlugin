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

namespace Tests\Sylius\RefundPlugin\Unit\Sender;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSender;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;

final class CreditMemoEmailSenderTest extends TestCase
{
    private SenderInterface&MockObject $sender;

    private CreditMemoFileResolverInterface&MockObject $creditMemoFileResolver;

    private CreditMemoFilePathResolverInterface&MockObject $creditMemoFilePathResolver;

    private CreditMemoEmailSender $creditMemoEmailSender;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sender = $this->createMock(SenderInterface::class);
        $this->creditMemoFileResolver = $this->createMock(CreditMemoFileResolverInterface::class);
        $this->creditMemoFilePathResolver = $this->createMock(CreditMemoFilePathResolverInterface::class);

        $this->creditMemoEmailSender = new CreditMemoEmailSender(
            $this->sender,
            true,
            $this->creditMemoFileResolver,
            $this->creditMemoFilePathResolver,
        );
    }

    /** @test */
    public function it_implements_credit_memo_email_sender_interface(): void
    {
        self::assertInstanceOf(CreditMemoEmailSenderInterface::class, $this->creditMemoEmailSender);
    }

    /** @test */
    public function it_sends_an_email_with_credit_memo_and_pdf_file_attachment_to_customer(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'Content of the credit memo');

        $this->creditMemoFileResolver
            ->expects(self::once())
            ->method('resolveByCreditMemo')
            ->with($creditMemo)
            ->willReturn($creditMemoPdf);

        $this->creditMemoFilePathResolver
            ->expects(self::once())
            ->method('resolve')
            ->with($creditMemoPdf)
            ->willReturn('/path/to/credit_memos/credit-memo.pdf');

        $this->sender
            ->expects(self::once())
            ->method('send')
            ->with('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo], ['/path/to/credit_memos/credit-memo.pdf']);

        $this->creditMemoEmailSender->send($creditMemo, 'john@example.com');
    }

    /** @test */
    public function it_sends_an_email_with_credit_memo_to_customer_without_pdf_file_attachment_if_pdf_generator_is_disabled(): void
    {
        $this->creditMemoEmailSender = new CreditMemoEmailSender(
            $this->sender,
            false,
            $this->creditMemoFileResolver,
            $this->creditMemoFilePathResolver,
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoFileResolver
            ->expects($this->never())
            ->method('resolveByCreditMemo');

        $this->creditMemoFilePathResolver
            ->expects($this->never())
            ->method('resolve');

        $this->sender
            ->expects(self::once())
            ->method('send')
            ->with('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo]);

        $this->creditMemoEmailSender->send($creditMemo, 'john@example.com');
    }
}
