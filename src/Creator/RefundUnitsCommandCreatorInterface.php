<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Symfony\Component\HttpFoundation\Request;

interface RefundUnitsCommandCreatorInterface
{
    public function fromRequest(Request $request): RefundUnits;
}
