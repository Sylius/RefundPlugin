<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;

final class CreditMemosContext implements Context
{
    public function __construct(private string $creditMemosPath)
    {
    }

    /**
     * @BeforeScenario
     */
    public function clearCreditMemosPath(): void
    {
        if (!is_dir($this->creditMemosPath)) {
            return;
        }

        foreach (scandir($this->creditMemosPath) as $file) {
            if (is_file($this->creditMemosPath . '/' . $file)) {
                unlink($this->creditMemosPath . '/' . $file);
            }
        }
    }
}
