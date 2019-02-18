<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Grid\Filter;

use Doctrine\ORM\Query\Expr\Comparison;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class ChannelFilterSpec extends ObjectBehavior
{
    function it_does_nothing_if_channel_is_not_defined(
        DataSourceInterface $dataSource
    ): void {
        $dataSource->restrict(Argument::any())->shouldNotBeCalled();

        $this->apply($dataSource, 'test', ['channel' => ''], []);
    }

    function it_restricts_data_for_specific_channel(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
        Comparison $comparison
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $expressionBuilder->equals('o.channel.code', 'DEFAULT')->willReturn($comparison);

        $dataSource->restrict($comparison)->shouldBeCalled();

        $this->apply($dataSource, 'test', ['channel' => 'DEFAULT'], []);
    }
}
