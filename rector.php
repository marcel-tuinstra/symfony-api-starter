<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->disableParallel();

    // Use Symfony + PHP 8.3 rules
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::NAMING,
        LevelSetList::UP_TO_PHP_83,
    ]);

    $rectorConfig->skip([
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]);

    $rectorConfig->importNames();
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');


    $rectorConfig->importShortClasses(false);
    $rectorConfig->removeUnusedImports(true);
};
