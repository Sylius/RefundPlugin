<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Sender;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\File\FileManagerInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    private const UNITS_REFUNDED = 'units_refunded';

    /** @var CreditMemoPdfFileGeneratorInterface */
    private $creditMemoPdfFileGenerator;

    /** @var SenderInterface */
    private $sender;

    /** @var FileManagerInterface */
    private $fileManager;

    public function __construct(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        SenderInterface $sender,
        FileManagerInterface $fileManager
    ) {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
        $this->sender = $sender;
        $this->fileManager = $fileManager;
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());

        $filePath = $creditMemoPdfFile->filename();
        $this->fileManager->createWithContent($filePath, $creditMemoPdfFile->content());

        $this->sender->send(
            self::UNITS_REFUNDED,
            [$recipient],
            ['creditMemo' => $creditMemo],
            [$this->fileManager->getBaseDirectory() . $filePath]
        );

        $this->fileManager->remove($filePath);
    }
}
