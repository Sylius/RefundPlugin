<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Normalizer;

interface MultipleMessagesNormalizerInterface
{
    public function normalize(array $messages): array;
}
