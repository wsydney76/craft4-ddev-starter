<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 */

use craft\helpers\App;
use Illuminate\Support\Collection;
use modules\base\BaseModule;
use modules\guide\GuideModule;
use modules\main\MainModule;

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',
    'modules' => [
        'main' => MainModule::class,
        'base' => BaseModule::class,
        'guide' => GuideModule::class,
    ],
    'bootstrap' => ['main', 'base', 'guide'],
    'components' => [
        'requestData' => function() {
            Collection::macro('addToList', function(string $key, mixed $value) {
                if ($this->has($key)) {
                    $this->put($key, array_merge($this->get($key), [$value]));
                } else {
                    $this->put($key, [$value]);
                }

                return $this;
            });
            return new Collection();
        },
    ]
];
