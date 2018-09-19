<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Normalizer;

final class MultipleMessagesNormalizer implements MultipleMessagesNormalizerInterface
{
    public function normalize(array $messages): array
    {
        $messagesCount = count($messages);

        if ($messagesCount > 1) {
            for ($arrayIndex = 0; $arrayIndex < $messagesCount - 1; $arrayIndex++) {
                $messages[$arrayIndex] .= '\n\n';
            }
        }

        return $messages;
    }
}
