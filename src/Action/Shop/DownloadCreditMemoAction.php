<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Shop;

use Sylius\RefundPlugin\Checker\CreditMemoCustomerRelationCheckerInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotAccessible;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadCreditMemoAction
{
    /** @var CreditMemoPdfFileGeneratorInterface */
    private $creditMemoPdfFileGenerator;

    /** @var CreditMemoCustomerRelationCheckerInterface */
    private $creditMemoCustomerRelationChecker;

    /** @var CreditMemoFileResponseBuilderInterface */
    private $creditMemoFileResponseBuilder;

    public function __construct(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoCustomerRelationCheckerInterface $creditMemoCustomerRelationChecker,
        CreditMemoFileResponseBuilderInterface $creditMemoFileResponseBuilder
    ) {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
        $this->creditMemoCustomerRelationChecker = $creditMemoCustomerRelationChecker;
        $this->creditMemoFileResponseBuilder = $creditMemoFileResponseBuilder;
    }

    public function __invoke(Request $request, int $id): Response
    {
        try {
            $this->creditMemoCustomerRelationChecker->check((string) $id);
        } catch (CreditMemoNotAccessible $exception) {
            return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($id);

        return $this->creditMemoFileResponseBuilder->build(Response::HTTP_OK, $creditMemoPdfFile);
    }
}
