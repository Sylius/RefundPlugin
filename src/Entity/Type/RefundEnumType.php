<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Sylius\RefundPlugin\Model\RefundType;

final class RefundEnumType extends Type
{
    public function getName(): string
    {
        return 'php_enum_refund_type';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'VARCHAR(256) COMMENT "php_enum_action"';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!RefundType::isValid($value)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
                $value,
                RefundType::class, implode('", "', RefundType::keys())))
            ;
        }

        return new RefundType($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }
}
