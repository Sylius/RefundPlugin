<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Sender;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;
use Symfony\Component\Filesystem\Filesystem;

final class CreditMemoEmailSenderSpec extends ObjectBehavior
{
    function let(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        Filesystem $filesystem,
        SenderInterface $sender
    ): void {
        $this->beConstructedWith($creditMemoPdfFileGenerator, $filesystem, $sender, '/web/media/temp/credit-memos/');
    }

    function it_implements_credit_memo_email_sender_interface()
    {
        $this->shouldImplement(CreditMemoEmailSenderInterface::class);
    }

    function it_sends_email_with_credit_memo_to_customer(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        Filesystem $filesystem,
        SenderInterface $sender,
        CreditMemoInterface $creditMemo
    ): void {
        $creditMemo->getId()->willReturn(1);

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'I am credit memo number #2018/10/000444');
        $creditMemoPdfFileGenerator->generate(1)->willReturn($creditMemoPdf);

        $filesystem
            ->dumpFile('/web/media/temp/credit-memos/credit-memo.pdf', 'I am credit memo number #2018/10/000444')
            ->shouldBeCalled()
        ;

        $sender
            ->send('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo], ['/web/media/temp/credit-memos/credit-memo.pdf'])
            ->shouldBeCalled()
        ;

        $filesystem->remove('/web/media/temp/credit-memos/credit-memo.pdf')->shouldBeCalled();

        $this->send($creditMemo, 'john@example.com');
    }
}
