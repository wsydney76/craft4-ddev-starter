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
 * @mixin ProjectService $project
 * @mixin ViteVariable $vite
 */

class CustomCraftVariable extends CraftVariable
{
    public function schema(): Schema
    {
        return new Schema();
    }

    public function imager(): ImagerVariable
    {
        return new ImagerVariable();
    }

    public function project(): ProjectService
    {
        return new ProjectService();
    }

    public function vite(): ViteVariable
    {
        return new ViteVariable();
    }
}