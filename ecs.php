<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecs): void {
    $ecs->paths([__DIR__ . '/src']);

    // Load predefined PHP-CS-Fixer rules
    $ecs->sets([
        SetList::COMMON,
        SetList::PSR_12,
        SetList::CLEAN_CODE,
    ]);

    // Enforce PSR-12 negation spacing (`if (!foo)` instead of `if (! foo)`)
    $ecs->skip([
        PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer::class,
    ]);
};
