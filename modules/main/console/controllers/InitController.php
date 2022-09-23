<?php

namespace modules\main\console\controllers;

use Craft;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\Assets;
use Faker\Factory;
use yii\console\ExitCode;
use function array_diff;
use function copy;
use function pathinfo;
use function scandir;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class InitController extends BaseController
{

    /**
     * @var string
     */
    public $defaultAction = 'all';

    public function actionAll(): int
    {

        if ($this->interactive && !$this->confirm('Run all init actions? This should only be done once, immediately after installing.')) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('Setting global content...');
        $this->actionSetupGlobals();
        $this->stdout(PHP_EOL);

        $this->stdout('Create one-off pages...') . PHP_EOL;
        $this->actionCreateEntries();
        $this->stdout(PHP_EOL);

        $this->stdout('Update Users...');
        $this->actionSetUsers();
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-entries', ['interactive' => $this->interactive]);

        Craft::$app->runAction('main/assets/create-transforms', ['interactive' => $this->interactive]);

        return ExitCode::OK;
    }

    public function actionSetupGlobals(): int
    {
        $faker = Factory::create();

        // Set Globals
        $global = GlobalSet::find()->handle('siteInfo')->site('en')->one();
        if ($global) {
            $global->setFieldValue('siteName', 'Starter');
            $global->setFieldValue('copyright', 'Starter GmbH');
            $global->setFieldValue('postalAddress', $faker->address());
            $global->setFieldValue('email', $faker->email());
            $global->setFieldValue('phoneNumber', $faker->phoneNumber());
            $global->setFieldValue('socialLinks', [
                ['col1' => 'twitter', 'col2' => 'https://twitter.com'],
                ['col1' => 'instagram', 'col2' => 'https://instagram.com'],
            ]);
            $global->setFieldValue('textModules' , [
               'sortOrder' => ['new1'],
               'blocks' => [
                   'new1' => [
                       'type' => 'textModule',
                       'fields' => [
                           'key' => 'youtubeConsent',
                           'heading' => 'External YouTube content',
                           'text' => 'This will probably send personal data to youtube'
                       ]
                   ]
               ]
            ]);
            Craft::$app->elements->saveElement($global);
        }

        $global = GlobalSet::find()->handle('siteInfo')->site('de')->one();
        if ($global) {
            $block = $global->textModules->one();
            $global->setFieldValue('textModules' , [
                'sortOrder' => [$block->id],
                'blocks' => [
                    $block->id => [
                        'type' => 'textModule',
                        'fields' => [
                            'key' => 'youtubeConsent',
                            'heading' => 'Externer YouTube-Inhalt',
                            'text' => 'Dies wird wahrscheinlich persönliche Daten YouTube übertragen'
                        ]
                    ]
                ]
            ]);
            Craft::$app->elements->saveElement($global);
        }

        return ExitCode::OK;
    }

    public function actionCreateEntries(): int
    {


        $homepage = $this->createEntry([
            'section' => 'page',
            'type' => 'home',
            'title' => 'Homepage',
            'slug' => '__home__'
        ]);

        if (!$homepage) {
            $this->stdout("Could not create homepage." . PHP_EOL);
            return ExitCode::OK;
        }

        $this->createEntry([
            'section' => 'page',
            'type' => 'sitemap',
            'title' => 'Sitemap',
            'slug' => 'sitemap',
            'fields' => [
                'tagline' => 'Overview of all entries'
            ],
            'localized' => [
                'de' => [
                    'fields' => [
                        'tagline' => 'Übersicht über alle Seiten'
                    ],
                ]
            ]
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'newsIndex',
            'title' => 'News',
            'slug' => 'news',
            'parent' => $homepage
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'default',
            'title' => 'About',
            'slug' => 'about',
            'parent' => $homepage,
            'fields' => [
                'tagline' => 'Who we are'
            ],
            'localized' => [
                'de' => [
                    'title' => 'Über uns',
                    'slug' => 'ueber-uns',
                    'fields' => [
                        'tagline' => 'Wer wir sind'
                    ],
                ]
            ]
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'contact',
            'title' => 'Contact',
            'slug' => 'contact',
            'parent' => $homepage,
            'localized' => [
                'de' => [
                    'title' => 'Kontakt',
                    'slug' => 'kontakt'
                ]
            ]
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'search',
            'title' => 'Search',
            'slug' => 'search',
            'parent' => $homepage,
            'localized' => [
                'de' => [
                    'title' => 'Suche',
                    'slug' => 'suche'
                ]
            ]
        ]);


        $this->createEntry([
            'section' => 'legal',
            'type' => 'default',
            'title' => 'Imprint',
            'slug' => 'imprint',
            'localized' => [
                'de' => [
                    'title' => 'Impressum',
                    'slug' => 'impressum'
                ]
            ]
        ]);

        $this->createEntry([
            'section' => 'legal',
            'type' => 'privacy',
            'title' => 'Privacy Declaration',
            'slug' => 'privacy',
            'fields' => [
                'bodyContent' => [
                    [
                        'type' => 'text',
                        'fields' => [
                            'text' => 'Content TDB.'
                        ]
                    ]
                ]
            ],
            'localized' => [
                'de' => [
                    'title' => 'Datenschutzerklärung',
                    'slug' => 'datenschutzerklaerung',
                    'fields' => [
                        'bodyContent' => [
                            [
                                'type' => 'text',
                                'fields' => [
                                    'text' => 'Inhalt TDB.'
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ]);

        return ExitCode::OK;
    }


    public function actionSetUsers(): int
    {
        $faker = Factory::create();

        // Set admin attributes
        $user = User::find()->one();
        $user->firstName = 'Sabine';
        $user->lastName = 'Mustermann';

        Craft::$app->elements->saveElement($user);

        // Add new user
        $user = new User();
        $user->username = 'erna';
        $user->firstName = 'Erna';
        $user->lastName = 'Klawuppke';
        $user->email = 'erna.klawuppke@example.com';

        $user->setScenario(User::SCENARIO_LIVE);

        if (Craft::$app->elements->saveElement($user)) {
            $group = Craft::$app->userGroups->getGroupByHandle('contentEditors');

            if ($group) {
                Craft::$app->users->assignUserToGroups($user->getId(), [$group->id]);
            }
        } else {
            echo "Error saving user";
        }


        // Set user photos
        $sourceDir = App::parseEnv('@storage/rebrand/userphotos');

        $files = scandir($sourceDir);
        $files = array_diff($files, ['.', '..']);

        foreach ($files as $file) {
            $path = $sourceDir . DIRECTORY_SEPARATOR . $file;
            $pathInfo = pathinfo($path);
            $username = $pathInfo['filename'];

            $user = User::find()->username($username)->one();
            if ($user) {
                // saveUserPhoto deletes the file, so making a temporary copy
                $tempPath = Assets::tempFilePath($pathInfo['extension']);
                copy($path, $tempPath);
                Craft::$app->users->saveUserPhoto($tempPath, $user);
            }
        }
        return ExitCode::OK;
    }


}
