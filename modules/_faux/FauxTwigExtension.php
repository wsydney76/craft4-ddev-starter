<?php
/**
 * FauxTwigExtension for Craft CMS 3.x
 *
 * This is intended to be used with the Symfony Plugin for PhpStorm:
 * https://plugins.jetbrains.com/plugin/7219-symfony-plugin
 *
 * It will provide full auto-complete for craft.app. and many other useful things
 * in your Twig templates.
 *
 * Place the file somewhere in your project or include it via PhpStorm Settings -> Include Path.
 * You never call it, it's never included anywhere via PHP directly nor does it affect other
 * classes or Twig in any way. But PhpStorm will index it, and think all those variables
 * are in every single template and thus allows you to use Intellisense auto-completion.
 *
 * Thanks to Robin Schambach; for context, see:
 * https://github.com/Haehnchen/idea-php-symfony2-plugin/issues/1103
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2019 nystudio107
 */

namespace modules\_faux;

use craft\elements\Asset;
use craft\elements\ElementCollection;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\MatrixBlock;
use craft\elements\User;
use craft\web\twig\variables\Paginate;
use spacecatninja\imagerx\variables\ImagerVariable;
use Spatie\SchemaOrg\Schema;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @author    nystudio107 / wsydney76
 * @package   FauxTwigExtension
 * @since     1.0.0
 */
class FauxTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            // Craft Variable with custom properties for plugin variables.
            // No idea why this does work sometimes, sometimes it doesn't
            'craft' => new CustomCraftVariable(),

            // Inserted by Craft CMS
            'entry' => new Entry(),

            // Custom variables, templates should use these names by convention

            // Craft Elements
            'image' => new Asset(),
            'block' => new MatrixBlock(),
            'user' => new User(),


            // Global Set
            'siteInfo' => new Entry(),

            // Collections (Query results)
            'entries' => new ElementCollection(),
            'blocks' => new ElementCollection(),
            'images' => new ElementCollection(),

            // Pagination
            'pageInfo' => new Paginate(),


            // Plugin variables
            // Use workaround if autocompletion for craft.xxx does not work
            // set schema = craft.schema
            'schema' => new Schema(),
            'imager' => new ImagerVariable()

        ];
    }
}
