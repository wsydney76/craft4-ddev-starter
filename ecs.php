<?php

declare(strict_types=1);

use craft\ecs\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function(ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();
    $ecsConfig->paths([
        __DIR__ . '/modules/base',
        __DIR__ . '/modules/guide',
        __DIR__ . '/modules/main',
        __FILE__,
    ]);

    // $ecsConfig->sets([SetList::CRAFT_CMS_3]); // for Craft 3 projects
    $ecsConfig->sets([SetList::CRAFT_CMS_4]); // for Craft 4 projects
};