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
use Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolverInterface;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Webmozart\Assert\Assert;

final class CreditMemoEmailSender implements CreditMemoEmailSenderInterface
{
    private const UNITS_REFUNDED = 'units_refunded';

    public function __construct(
        private SenderInterface $sender,
        private bool $hasEnabledPdfFileGenerator,
        private CreditMemoFileResolverInterface $creditMemoFileResolver,
        private CreditMemoFilePathResolverInterface $creditMemoFilePathResolver,
    ) {
    }

    public function send(CreditMemoInterface $creditMemo, string $recipient): void
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            $this->sender->send(self::UNITS_REFUNDED, [$recipient], ['creditMemo' => $creditMemo]);

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
}
