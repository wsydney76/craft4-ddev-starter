<?php

namespace modules\_faux;

use craft\web\twig\variables\CraftVariable;
use modules\main\services\ProjectService;
use nystudio107\vite\variables\ViteVariable;
use spacecatninja\imagerx\variables\ImagerVariable;
use Spatie\SchemaOrg\Schema;

/**
 * @mixin Schema $schema
 * @mixin ImagerVariable $imager
 * @property  ProjectService $project
 * @property Schema $schema
 * @mixin ViteVariable $vite
 */

class CustomCraftVariable extends CraftVariable
{
    public function imager(): ImagerVariable
    {
        return new ImagerVariable();
    }


    public function vite(): ViteVariable
    {
        return new ViteVariable();
    }
}