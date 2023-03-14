<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $containerConfigurator): void
{
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');

    $containerConfigurator->services()->set(HeaderCommentFixer::class)->call('configure', [[
        'location' => 'after_open',
        'header' =>
'This file is part of the Sylius package.

(c) Paweł Jędrzejewski

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.',
    ]]);

    $containerConfigurator->skip([
        VisibilityRequiredFixer::class => ['*Spec.php'],
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        '**/var/*',
    ]);
};
