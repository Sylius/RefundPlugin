<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Converter\RequestToRefundUnitsConverterInterface;
use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class RefundUnitsCommandCreator implements RequestCommandCreatorInterface
{
    public function __construct(private RequestToRefundUnitsConverterInterface $requestToRefundUnitsConverter)
    {
    }

    public function fromRequest(Request $request): RefundUnits
    {
        Assert::true($request->attributes->has('orderNumber'), 'Refunded order number not provided');

        $units = $this->requestToRefundUnitsConverter->convert($request);

        if (count($units) === 0) {
            throw InvalidRefundAmount::withValidationConstraint('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        /** @var string $comment */
        $comment = $request->request->get('sylius_refund_comment', '');

        return new RefundUnits(
            $request->attributes->get('orderNumber'),
            $units,
            (int) $request->request->get('sylius_refund_payment_method'),
            $comment,
        );
    }
}
