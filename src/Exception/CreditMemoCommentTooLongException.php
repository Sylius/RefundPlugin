<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Exception;

final class CreditMemoCommentTooLongException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct(sprintf('Credit memo comment is too long!'));
    }
}
