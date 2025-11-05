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
};
