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

namespace Sylius\RefundPlugin\Action\Shop;

use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotAccessible;
use Sylius\RefundPlugin\Resolver\CreditMemoFileResolverInterface;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadCreditMemoAction
{
    public function __construct(
        private CreditMemoFileResolverInterface $creditMemoFileResolver,
        private CreditMemoCustomerRelationCheckerInterface $creditMemoCustomerRelationChecker,
        private CreditMemoFileResponseBuilderInterface $creditMemoFileResponseBuilder,
        private bool $hasEnabledPdfFileGenerator,
    ) {
    }

    public function __invoke(Request $request, string $id): Response
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        try {
            $this->creditMemoCustomerRelationChecker->check($id);
        } catch (CreditMemoNotAccessible $exception) {
            return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        $creditMemoPdfFile = $this->creditMemoFileResolver->resolveById($id);

        return $this->creditMemoFileResponseBuilder->build(Response::HTTP_OK, $creditMemoPdfFile);
    }
}
