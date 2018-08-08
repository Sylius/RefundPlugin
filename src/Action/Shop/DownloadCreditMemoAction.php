<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Action\Shop;

use Sylius\Bundle\CoreBundle\Context\CustomerContext;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadCreditMemoAction
{
    /** @var CreditMemoPdfFileGeneratorInterface */
    private $creditMemoPdfFileGenerator;

    /** @var CustomerContext */
    private $customerContext;

    /** @var RepositoryInterface */
    private $creditMemoRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CustomerContext $customerContext,
        RepositoryInterface $creditMemoRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->creditMemoPdfFileGenerator = $creditMemoPdfFileGenerator;
        $this->customerContext = $customerContext;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request, int $id): Response
    {
        /** @var CreditMemoInterface $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($id);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($creditMemo->getOrderNumber());

        /** @var CustomerInterface $orderCustomer */
        $orderCustomer = $order->getCustomer();

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        if ($orderCustomer->getId() !== $customer->getId()) {
            return new Response(Response::HTTP_UNAUTHORIZED);
        }

        $creditMemoPdfFile = $this->creditMemoPdfFileGenerator->generate($id);

        $response = new Response($creditMemoPdfFile->content(), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $creditMemoPdfFile->filename()),
        ]);

        return $response;
    }
}
