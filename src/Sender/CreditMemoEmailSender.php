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

namespace Sylius\RefundPlugin\Sender;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\File\FileManagerInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    private const UNITS_REFUNDED = 'units_refunded';

    private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator;

    private SenderInterface $sender;

    private FileManagerInterface $fileManager;

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
            [$this->fileManager->realPath($filePath)]
        );

        $this->fileManager->remove($filePath);
    }
}
