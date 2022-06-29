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
use Sylius\RefundPlugin\Provider\CreditMemoFileProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    private const UNITS_REFUNDED = 'units_refunded';

    public function __construct(
        private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        private SenderInterface $sender,
        private FileManagerInterface $fileManager,
        private bool $hasEnabledPdfFileGenerator,
        private ?CreditMemoFileProviderInterface $creditMemoFileProvider = null,
    ) {
        if (null === $this->creditMemoFileProvider) {
            @trigger_error(
                sprintf('Not passing a $creditMemoFileProvider to %s constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.', self::class),
                \E_USER_DEPRECATED
            );
        }
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            $this->sender->send(self::UNITS_REFUNDED, [$recipient], ['creditMemo' => $creditMemo]);

            return;
        }

        if (null === $this->creditMemoFileProvider) {
            $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());

            $creditMemoPdfPath = $creditMemoPdfFile->filename();
            $this->fileManager->createWithContent($creditMemoPdfPath, $creditMemoPdfFile->content());

            $this->sendCreditMemo($creditMemo, $recipient, $this->fileManager->realPath($creditMemoPdfPath));

            $this->fileManager->remove($creditMemoPdfPath);

            return;
        }

        $creditMemoPdf = $this->creditMemoFileProvider->provide($creditMemo);
        $creditMemoPdfPath = $creditMemoPdf->fullPath();
        Assert::notNull($creditMemoPdfPath);

        $this->sendCreditMemo($creditMemo, $recipient, $creditMemoPdfPath);
    }

    private function sendCreditMemo(CreditMemoInterface $creditMemo, string $recipient, string $filePath): void
    {
        $this->sender->send(
            self::UNITS_REFUNDED,
            [$recipient],
            ['creditMemo' => $creditMemo],
            [$filePath]
        );
    }
}
