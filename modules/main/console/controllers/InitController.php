<?php

namespace modules\main\console\controllers;

use Craft;
use craft\elements\Asset;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\Assets;
use craft\helpers\Console;
use Faker\Factory;
use yii\console\ExitCode;
use function str_replace;


class InitController extends BaseController
{

    /**
     * @var string
     */
    public $defaultAction = 'all';
    public int $minWidth = 1200;


    /**
     * Run all init actions
     *
     * @return int
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionAll(): int
    {

        if ($this->interactive && !$this->confirm('Run all init actions? This should only be done once, immediately after installing.', true)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout('Setting project specific values...' . PHP_EOL);
        $this->actionSetupDotEnv();
        $this->stdout(PHP_EOL);

        $this->stdout('Setting global content...' . PHP_EOL);
        $this->actionSetupGlobals();
        $this->stdout(PHP_EOL);

        $this->stdout('Create one-off pages...' . PHP_EOL);
        $this->actionCreateEntries();
        $this->stdout(PHP_EOL);

        $this->stdout('Update Users...' . PHP_EOL);
        $this->actionSetUsers();
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-topics', ['interactive' => $this->interactive]);

        Craft::$app->runAction('main/seed/create-entries', ['interactive' => $this->interactive]);

        Craft::$app->runAction('main/seed/create-homepage-content', ['interactive' => $this->interactive]);

        Craft::$app->runAction('main/assets/create-transforms', ['interactive' => $this->interactive]);

        return ExitCode::OK;
    }


    /**
     * Sets values that will be stored in the local .env file
     *
     * @return int
     * @throws \yii\base\Exception
     */
    public function actionSetupDotEnv(): int
    {
        if (!$this->interactive) {
            return ExitCode::OK;
        }

        $vars = [
            ['prompt' => 'System Name (for backend)', 'name' => 'SYSTEM_NAME'],
            ['prompt' => 'System mail address', 'name' => 'EMAIL_ADDRESS'],
            ['prompt' => 'System mail sender', 'name' => 'EMAIL_SENDER'],
        ];

        foreach ($vars as $var) {
            $value = $this->prompt($var['prompt'] . ': ', ['default' => App::env($var['name']), 'required' => true]);
            Craft::$app->config->setDotEnvVar($var['name'], $value);
        }

        return ExitCode::OK;
    }

    /**
     * Sets (mostly faked) field values for the siteInfo global set
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionSetupGlobals(): int
    {
        $faker = Factory::create();

        $siteName = 'Starter';
        $copyright = 'Starter GmbH';

        if ($this->interactive) {
            $siteName = $this->prompt('Site Name (for frontend): ', ['default' => $siteName, 'required' => true]);
            $copyright = $this->prompt('Copyright: ', ['default' => $copyright, 'required' => true]);
        }


        // Set Globals
        $global = GlobalSet::find()->handle('siteInfo')->site('en')->one();
        if ($global) {
            $global->setFieldValue('siteName', $siteName);
            $global->setFieldValue('copyright', $copyright);
            $global->setFieldValue('postalAddress', $faker->address());
            $global->setFieldValue('email', App::env('EMAIL_ADDRESS'));
            $global->setFieldValue('phoneNumber', $faker->phoneNumber());
            $global->setFieldValue('featuredImage', [$this->getImagesFromFolder('starter/')[0]->id ?? null]);
            $global->setFieldValue('socialLinks', [
                ['col1' => 'mastodon', 'col2' => 'https://joinmastodon.org'],
                ['col1' => 'instagram', 'col2' => 'https://instagram.com'],
            ]);
            $global->setFieldValue('textModules', [
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
            $global->setFieldValue('textModules', [
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

    /**
     * Creates page/legal entries
     *
     * @return int
     */
    public function actionCreateEntries(): int
    {

        $faker = Factory::create();

        $homepage = $this->createEntry([
            'section' => 'page',
            'type' => 'home',
            'site' => 'en',
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
            'site' => 'en',
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
            'site' => 'en',
            'title' => 'News',
            'slug' => 'news',
            'parent' => $homepage
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'topicIndex',
            'site' => 'en',
            'title' => 'Topics',
            'slug' => 'topics',
            'parent' => $homepage,
            'localized' => [
                'de' => [
                    'title' => 'Themen',
                    'slug' => 'themen'
                ]
            ]
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'default',
            'site' => 'en',
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
            'site' => 'en',
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
            'site' => 'en',
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
            'site' => 'en',
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
            'site' => 'en',
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


    /**
     * Sets name for the default user and creates another faked user. Sets user photos.
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\ImageException
     * @throws \craft\errors\VolumeException
     * @throws \yii\base\Exception
     */
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
        $user->active = true;

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

    protected function getImagesFromFolder(string $path, $minWidth = null, $limit = null)
    {
        $folder = Craft::$app->assets->findFolder(['path' => $path]);
        if (!$folder) {
            Console::output('Folder not found');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (!$minWidth) {
            $minWidth = $this->minWidth;
        }

        $query = Asset::find()
            ->kind('image')
            ->volume($this->volume)
            ->folderId($folder->id)
            ->width('> ' . $minWidth)
            ->limit($limit)
            ->orderBy(Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()');


        return $query->collect();
    }

    protected function getMarkdownParagraphs(int $number)
    {
        $paragraphs = '';
        foreach ($this->faker->paragraphs($number) as $paragraph) {
            $paragraphs .= $paragraph . PHP_EOL . PHP_EOL;
        }
        return $paragraphs;
    }

    protected function indexImages(): void
    {
        $imagesCount = Asset::find()
            ->volume($this->volume)
            ->kind('image')
            ->width("> $this->minWidth")
            ->count();

        if ($imagesCount < 20) {
            $this->stdout("Indexing existing images..." . PHP_EOL);
            Craft::$app->runAction('index-assets/one', [$this->volume]);
        }
    }


}
