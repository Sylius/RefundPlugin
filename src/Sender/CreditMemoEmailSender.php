<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Sender;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Symfony\Component\Filesystem\Filesystem;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    /** @var CreditMemoPdfFileGeneratorInterface */
    private $creditMemoPdfFileGenerator;

    /** @var Filesystem */
    private $filesystem;

    /** @var SenderInterface */
    private $sender;

    /** @var string */
    private $temporaryCreditMemoPath;

    public function __construct(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        Filesystem $filesystem,
        SenderInterface $sender,
        string $temporaryCreditMemoPath
    ) {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
        $this->filesystem = $filesystem;
        $this->sender = $sender;
        $this->temporaryCreditMemoPath = $temporaryCreditMemoPath;
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());

        $filePath = $this->temporaryCreditMemoPath.$creditMemoPdfFile->filename();
        $this->filesystem->dumpFile($filePath, $creditMemoPdfFile->content() );

        $this->sender->send('units_refunded', [$recipient], ['creditMemo' => $creditMemo], [$filePath]);

        $this->filesystem->remove($filePath);
    }
}
