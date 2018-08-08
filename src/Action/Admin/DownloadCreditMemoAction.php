<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Admin;

use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadCreditMemoAction
{
    /** @var CreditMemoPdfFileGeneratorInterface */
    private $creditMemoPdfFileGenerator;

    public function __construct(CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator)
    {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($id);

        $response = new Response($creditMemoPdfFile->content(), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $creditMemoPdfFile->filename()),
        ]);

        return $response;
    }
}
