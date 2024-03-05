<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $containerConfigurator): void
{
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');

    $containerConfigurator->parallel();
    $containerConfigurator->paths([
        'src/',
        'spec/',
    ]);

    $containerConfigurator->skip([
        VisibilityRequiredFixer::class => ['*Spec.php'],
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        'src/Resources/config/**',
        '**/var/*',
    ]);

    $containerConfigurator->ruleWithConfiguration(
        HeaderCommentFixer::class,
        [
            'location' => 'after_open',
            'comment_type' => HeaderCommentFixer::HEADER_COMMENT,
            'header' => <<<TEXT
This file is part of the Sylius package.

(c) Sylius Sp. z o.o.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
TEXT
        ]
    );
};
