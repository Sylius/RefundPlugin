<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\ResponseBuilder;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\HttpFoundation\Response;

final class CreditMemoFileResponseBuilder implements CreditMemoFileResponseBuilderInterface
{
    public function build(int $status, CreditMemoPdf $creditMemoPdfFile): Response
    {
        return new PdfResponse($creditMemoPdfFile->content(), $creditMemoPdfFile->filename());
    }
}
