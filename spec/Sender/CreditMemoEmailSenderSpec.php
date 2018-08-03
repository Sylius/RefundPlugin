<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Sender;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\File\FileManagerInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Sender\CreditMemoEmailSenderInterface;

final class CreditMemoEmailSenderSpec extends ObjectBehavior
{
    function let(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        SenderInterface $sender,
        FileManagerInterface $fileManager
    ): void {
        $this->beConstructedWith($creditMemoPdfFileGenerator, $sender, $fileManager);
    }

    function it_implements_credit_memo_email_sender_interface()
    {
        $this->shouldImplement(CreditMemoEmailSenderInterface::class);
    }

    function it_sends_email_with_credit_memo_to_customer(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        SenderInterface $sender,
        FileManagerInterface $fileManager,
        CreditMemoInterface $creditMemo
    ): void {
        $creditMemo->getId()->willReturn(1);

        $creditMemoPdf = new CreditMemoPdf('credit-memo.pdf', 'I am credit memo number #2018/10/000444');
        $creditMemoPdfFileGenerator->generate(1)->willReturn($creditMemoPdf);

        $fileManager->createWithContent('credit-memo.pdf', 'I am credit memo number #2018/10/000444')->shouldBeCalled();

        $fileManager->getBaseDirectory()->willReturn('/base/directory/');

        $sender
            ->send('units_refunded', ['john@example.com'], ['creditMemo' => $creditMemo], ['/base/directory/credit-memo.pdf'])
            ->shouldBeCalled()
        ;

        $fileManager->remove('credit-memo.pdf')->shouldBeCalled();

        $this->send($creditMemo, 'john@example.com');
    }
}
