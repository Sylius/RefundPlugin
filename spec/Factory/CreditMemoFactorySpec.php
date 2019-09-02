<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoChannel;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;

final class CreditMemoFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(CreditMemo::class);
    }

    function it_implements_sequence_factory_interface(): void
    {
        $this->shouldImplement(CreditMemoFactoryInterface::class);
    }

    function it_creates_new_credit_memo_for_data(
        CreditMemoChannel $channel,
        CustomerBillingDataInterface $from,
        ShopBillingData $to
    ): void {
        $issuedAt = new \DateTime();

        $this->createForData(
            '123-123-123',
            'CM-0001',
            'O-0001',
            3400,
            'USD',
            'US',
            $channel,
            [],
            'Comment',
            $issuedAt,
            $from,
            $to
        )->shouldBeLike(
            new CreditMemo(
                '123-123-123',
                'CM-0001',
                'O-0001', 3400,
                'USD', 'US',
                $channel->getWrappedObject(),
                [],
                'Comment',
                $issuedAt,
                $from->getWrappedObject(),
                $to->getWrappedObject()
            )
        );
    }
}
