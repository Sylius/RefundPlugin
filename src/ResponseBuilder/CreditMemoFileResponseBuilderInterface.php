<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ResponseBuilder;

use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\HttpFoundation\Response;

interface CreditMemoFileResponseBuilderInterface
{
    public function build(int $status, CreditMemoPdf $creditMemoPdfFile): Response;
}
