<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CreditMemoUnitGeneratorInterface */
    private $orderItemUnitCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $shipmentCreditMemoUnitGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemUnitCreditMemoUnitGenerator = $orderItemUnitCreditMemoUnitGenerator;
        $this->shipmentCreditMemoUnitGenerator = $shipmentCreditMemoUnitGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
    }

    public function generate(
        string $orderNumber,
        int $total,
        array $unitIds,
        array $shipmentIds,
        string $comment
    ): CreditMemoInterface {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        if ($order === null) {
            throw OrderNotFound::withNumber($orderNumber);
        }

        $creditMemoUnits = [];

        foreach ($unitIds as $unitId) {
            $creditMemoUnits[] = $this->orderItemUnitCreditMemoUnitGenerator->generate($unitId)->serialize();
        }

        foreach ($shipmentIds as $shipmentId) {
            $creditMemoUnits[] = $this->shipmentCreditMemoUnitGenerator->generate($shipmentId)->serialize();
        }

        return new CreditMemo(
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $creditMemoUnits,
            $comment
        );
    }
}
