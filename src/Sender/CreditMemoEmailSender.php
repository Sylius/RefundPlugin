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
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Webmozart\Assert\Assert;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    private const UNITS_REFUNDED = 'units_refunded';

    public function __construct(
        private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        private SenderInterface $sender,
        private FileManagerInterface $fileManager,
        private bool $hasEnabledPdfFileGenerator,
        private ?CreditMemoFileResolverInterface $creditMemoFileResolver = null,
        private ?CreditMemoFilePathResolverInterface $creditMemoFilePathResolver = null,
    ) {
        if (null === $this->creditMemoFileResolver) {
            @trigger_error(
                sprintf('Not passing a $creditMemoFileResolver to %s constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.', self::class),
                \E_USER_DEPRECATED,
            );
        }

        if (null === $this->creditMemoFilePathResolver) {
            @trigger_error(
                sprintf('Not passing a $creditMemoFilePathResolver to %s constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.', self::class),
                \E_USER_DEPRECATED,
            );
        }
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            $this->sender->send(self::UNITS_REFUNDED, [$recipient], ['creditMemo' => $creditMemo]);

            return;
        }

        if (null === $this->creditMemoFileResolver || null === $this->creditMemoFilePathResolver) {
            $this->sendCreditMemoWithTemporaryFile($creditMemo, $recipient);

            return;
        }

        $creditMemoPdf = $this->creditMemoFileResolver->resolveByCreditMemo($creditMemo);
        $creditMemoPdfPath = $this->creditMemoFilePathResolver->resolve($creditMemoPdf);
        Assert::notNull($creditMemoPdfPath);

        $this->sendCreditMemo($creditMemo, $recipient, $creditMemoPdfPath);
    }

    private function sendCreditMemo(CreditMemoInterface $creditMemo, string $recipient, string $filePath): void
    {
        $this->sender->send(
            self::UNITS_REFUNDED,
            [$recipient],
            ['creditMemo' => $creditMemo],
            [$filePath],
        );
    }

    private function sendCreditMemoWithTemporaryFile(CreditMemoInterface $creditMemo, string $recipient): void
    {
        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($creditMemo->getId());

        $creditMemoPdfPath = $creditMemoPdfFile->filename();
        $this->fileManager->createWithContent($creditMemoPdfPath, $creditMemoPdfFile->content());

        $this->sendCreditMemo($creditMemo, $recipient, $this->fileManager->realPath($creditMemoPdfPath));

        $this->fileManager->remove($creditMemoPdfPath);
    }
}
