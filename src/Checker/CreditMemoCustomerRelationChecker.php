<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotAccessible;

final class CreditMemoCustomerRelationChecker implements CreditMemoCustomerRelationCheckerInterface
{
    /** @var CustomerContextInterface */
    private $customerContext;

    /** @var RepositoryInterface */
    private $creditMemoRepository;

    public function __construct(
        CustomerContextInterface $customerContext,
        RepositoryInterface $creditMemoRepository
    ) {
        $this->customerContext = $customerContext;
        $this->creditMemoRepository = $creditMemoRepository;
    }

    public function check(string $creditMemoId): void
    {
        /** @var CreditMemoInterface $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);
        $order = $creditMemo->getOrder();

        /** @var CustomerInterface $orderCustomer */
        $orderCustomer = $order->getCustomer();

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        if ($orderCustomer->getId() !== $customer->getId()) {
            throw CreditMemoNotAccessible::withUserId($creditMemoId, $customer->getId());
        }
    }
}
