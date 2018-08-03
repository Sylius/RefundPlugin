<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Element;

interface PdfDownloadElementInterface
{
    public function isPdfFileDownloaded(): bool;
}
