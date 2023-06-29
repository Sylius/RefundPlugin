<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Services\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;

final class FailedCreditMemoGenerator implements CreditMemoGeneratorInterface
{
    public const FAILED_FILE = __DIR__.'/credit-memo-failed.json';

    private CreditMemoGeneratorInterface $baseCreditMemoGenerator;

    public function __construct(CreditMemoGeneratorInterface $baseCreditMemoGenerator)
    {
        $this->baseCreditMemoGenerator = $baseCreditMemoGenerator;
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        string $comment
    ): CreditMemoInterface {
        if (file_exists(self::FAILED_FILE)) {
            unlink(self::FAILED_FILE);

            throw new \Exception('Credit memo generation failed');
        }

        return $this->baseCreditMemoGenerator->generate($order, $total, $units, $comment);
    }

    public function failCreditMemoGeneration(): void
    {
        touch(self::FAILED_FILE);
    }
}
