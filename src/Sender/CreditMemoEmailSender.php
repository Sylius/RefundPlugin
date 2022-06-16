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

    public function __construct(
        private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        private SenderInterface $sender,
        private FileManagerInterface $fileManager,
        private bool $hasEnabledPdfFileGenerator
    ) {
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        $templateParameters = [
            'creditMemo' => $creditMemo,
            'order' => $creditMemo->getOrder(),
            'localeCode' => $creditMemo->getLocaleCode(),
            'channel' => $creditMemo->getChannel(),
        ];

        if (!$this->hasEnabledPdfFileGenerator) {
            $this->sender->send(self::UNITS_REFUNDED, [$recipient], $templateParameters);

            return;
        }

        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());

        $filePath = $creditMemoPdfFile->filename();
        $this->fileManager->createWithContent($filePath, $creditMemoPdfFile->content());

        $this->sender->send(
            self::UNITS_REFUNDED,
            [$recipient],
            $templateParameters,
            [$this->fileManager->realPath($filePath)]
        );

        $this->fileManager->remove($filePath);
    }
}
