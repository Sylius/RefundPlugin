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

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadCreditMemoAction
{
    private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator;

    private CreditMemoFileResponseBuilderInterface $creditMemoFileResponseBuilder;

    public function __construct(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileResponseBuilderInterface $creditMemoFileResponseBuilder
    ) {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
        $this->creditMemoFileResponseBuilder = $creditMemoFileResponseBuilder;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($id);

        return $this->creditMemoFileResponseBuilder->build(Response::HTTP_OK, $creditMemoPdfFile);
    }
}
