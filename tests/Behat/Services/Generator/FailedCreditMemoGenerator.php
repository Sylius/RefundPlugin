<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Services\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;

final class FailedCreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var CreditMemoGeneratorInterface */
    private $baseCreditMemoGenerator;

    public function __construct(CreditMemoGeneratorInterface $baseCreditMemoGenerator)
    {
        $this->baseCreditMemoGenerator = $baseCreditMemoGenerator;
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        array $shipments,
        string $comment
    ): CreditMemoInterface {
        if (file_exists(__DIR__.'/credit-memo-failed.json')) {
            unlink(__DIR__.'/credit-memo-failed.json');

            throw new \Exception('Credit memo generation failed');
        }

        return $this->baseCreditMemoGenerator->generate($order, $total, $units, $shipments, $comment);
    }

    public function failCreditMemoGeneration(): void
    {
        touch(__DIR__.'/credit-memo-failed.json');
    }
}
