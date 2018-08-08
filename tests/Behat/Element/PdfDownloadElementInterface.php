<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Element;

interface PdfDownloadElementInterface
{
    public function isPdfFileDownloaded(): bool;
}
