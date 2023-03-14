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

namespace Sylius\RefundPlugin\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use MyCLabs\Enum\Enum;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

class RefundEnumType extends Type
{
    public function getName(): string
    {
        return 'sylius_refund_refund_type';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(256)';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): RefundTypeInterface
    {
        if ($value instanceof RefundTypeInterface && $value instanceof Enum && !$value::isValid($value)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
                (string) $value->getValue(),
                RefundTypeInterface::class,
                implode('", "', $value::keys()),
            ));
        }

        return $this->createType($value);
    }

    /** @param mixed $value */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof RefundTypeInterface) {
            return (string) $value->getValue();
        }

        throw ConversionException::conversionFailed((string) $value, 'sylius_refund_refund_type');
    }

    protected function createType(string $value): RefundTypeInterface
    {
        return new RefundType($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
