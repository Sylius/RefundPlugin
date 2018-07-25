<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CreditMemoDetailsAction
{
    /** @var ObjectRepository */
    private $creditMemoRepository;

    /** @var Environment */
    private $twig;

    public function __construct(ObjectRepository $creditMemoRepository, Environment $twig)
    {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $creditMemo = $this->creditMemoRepository->findOneBy([
            'orderNumber' => $request->attributes->get('orderNumber'),
            'id' => $request->attributes->get('id'),
        ]);

        return new Response(
            $this->twig->render('@SyliusRefundPlugin/Order/Admin/CreditMemo/details.html.twig', [
                'creditMemo' => $creditMemo,
            ])
        );
    }
}
