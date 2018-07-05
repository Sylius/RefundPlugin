<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;

final class RefundingContext implements Context
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CommandBus */
    private $commandBus;

    /** @var OrderInterface|null */
    private $order;

    public function __construct(OrderRepositoryInterface $orderRepository, CommandBus $commandBus)
    {
        $this->orderRepository = $orderRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @When I want to refund some units of order :orderNumber
     */
    public function wantToRefundSomeUnitsOfOrder(string $orderNumber): void
    {
        $this->order = $this->orderRepository->findOneByNumber($orderNumber);
    }

    /**
     * @When /^I decide to refund (\d)st "([^"]+)" product$/
     */
    public function decideToRefundProduct(int $unitNumber, string $productName): void
    {
        $unit = $this->order->getItemUnits()->get($unitNumber);

        $this->commandBus->dispatch(new RefundUnits($this->order->getNumber(), [$unit->getId()]));
    }

    /**
     * @Then refunded total should be :refundedTotal
     */
    public function refundedTotalShouldBe(string $refundedTotal): void
    {
        // TODO: check refunded total with service
    }

    /**
     * @Then /^I should not be able to refund (\d)st unit with product "([^"]+)"$/
     */
    public function shouldNotBeAbleToRefundUnitWithProduct(int $unitNumber, string $productName): void
    {
        $unit = $this->order->getItemUnits()->get($unitNumber);

        try {
            $this->commandBus->dispatch(new RefundUnits($this->order->getNumber(), [$unit->getId()]));
        } catch (CommandDispatchException $exception) {
            return;
        }

        throw new \Exception('RefundUnits command should fail');
    }

    /**
     * @Then /^I should(?:| still) be able to refund (\d)(?:|st|nd|rd) unit with product "([^"]+)"$/
     */
    public function shouldBeAbleToRefundUnitWithProduct(int $unitNumber, string $productName): void
    {
        $unit = $this->order->getItemUnits()->get($unitNumber);

        try {
            $this->commandBus->dispatch(new RefundUnits($this->order->getNumber(), [$unit->getId()]));
        } catch (CommandDispatchException $exception) {
            throw new \Exception('RefundUnits command should not fail');
        }
    }

    /**
     * @Then I should be notified that selected order units have been successfully refunded
     */
    public function shouldBeNotifiedThatSelectedOrderUnitsHaveBeenSuccessfullyRefunded(): void
    {
        // intentionally left blank - not relevant in application scope
    }
}
