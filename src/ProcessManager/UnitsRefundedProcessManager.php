<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\ProcessManager;

use Sylius\RefundPlugin\Event\UnitsRefunded;

final class UnitsRefundedProcessManager implements UnitsRefundedProcessManagerInterface
{
    /** @var UnitsRefundedProcessStepInterface */
    private $creditMemoProcessManager;

    /** @var UnitsRefundedProcessStepInterface */
    private $refundPaymentProcessManager;

    public function __construct(
        UnitsRefundedProcessStepInterface $creditMemoProcessManager,
        UnitsRefundedProcessStepInterface $refundPaymentProcessManager
    ) {
        $this->creditMemoProcessManager = $creditMemoProcessManager;
        $this->refundPaymentProcessManager = $refundPaymentProcessManager;
    }

    public function __invoke(UnitsRefunded $event): void
    {
        $this->creditMemoProcessManager->next($event);
        $this->refundPaymentProcessManager->next($event);
    }
}
