<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ResponseBuilder;

use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\HttpFoundation\Response;

final class CreditMemoFileResponseBuilder implements CreditMemoFileResponseBuilderInterface
{
    public function build(int $status, CreditMemoPdf $creditMemoPdfFile): Response
    {
        $response = new Response($creditMemoPdfFile->content(), $status, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $creditMemoPdfFile->filename()),
        ]);

        return $response;
    }
}
