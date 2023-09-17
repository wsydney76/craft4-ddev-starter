<?php

namespace modules\main\console\controllers;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\Assets;
use craft\helpers\Console;
use Faker\Factory;
use Faker\Generator;
use yii\console\ExitCode;
use yii\helpers\Markdown;

class InitController extends BaseController
{
    /**
     * @var string
     */
    public $defaultAction = 'all';
    public int $minWidth = 1200;
    public string $volume = 'images';

    protected Generator $faker;


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

        if ($this->interactive && !$this->confirm('Seed database with fake elements?', true)) {
            Console::output('Skipping database seeding!');
            Console::output('See craft help main/init and craft help main/seed for available options.');
            return ExitCode::OK;
        }

        $retrievePages = true;
        if ($this->interactive && !$this->confirm('Retrieve pages after creation? That will create image transforms, but will take some time.', true)) {
            $retrievePages = false;
        }
        $this->stdout(PHP_EOL);

        $this->stdout('Creating one-off pages.' . PHP_EOL);
        $this->actionCreateEntries();
        $this->stdout(PHP_EOL);

        $this->stdout('Updating Users.' . PHP_EOL);
        $this->actionSetUsers();
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-topics', ['interactive' => false]);
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-articles', ['interactive' => false]);
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-stories', ['interactive' => false]);
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-homepage-content', ['interactive' => false]);
        $this->stdout(PHP_EOL);

        if ($retrievePages) {
            Craft::$app->runAction('main/assets/create-transforms', ['interactive' => false]);
            $this->stdout(PHP_EOL);
        }

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
     * Sets (mostly faked) field values for the siteInfo single
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionSetupGlobals(): int
    {
        $faker = Factory::create('de_DE');

        $siteInfo = Entry::find()->section('siteInfo')->one();

        $siteName = App::env('SYSTEM_NAME') ?? 'Starter';
        $copyright = $siteInfo->copyright ?? 'Starter GmbH';

        if ($this->interactive) {
            $siteName = $this->prompt('Site Name (for frontend): ', ['default' => $siteName, 'required' => true]);
            $copyright = $this->prompt('Copyright: ', ['default' => $copyright, 'required' => true]);
        }


        // Set Globals
        $global = Entry::find()->section('siteInfo')->site('en')->one();
        if ($global) {
            $global->title = $siteName;
            $global->setFieldValue('copyright', $copyright);
            $global->setFieldValue('postalAddress', $faker->address());
            $global->setFieldValue('email', App::env('EMAIL_ADDRESS'));
            $global->setFieldValue('phoneNumber', $faker->phoneNumber());
            $global->setFieldValue('featuredImage', [$this->getImagesFromFolder('starter/')[0]->id ?? null]);
            $global->setFieldValue('socialLinks', [
                ['col1' => 'mastodon', 'col2' => 'https://joinmastodon.org'],
                ['col1' => 'instagram', 'col2' => 'https://instagram.com'],
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
        $homepage = $this->createEntry([
            'section' => 'page',
            'type' => 'home',
            'site' => 'en',
            'title' => 'Homepage',
            'slug' => '__home__',
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
                'tagline' => 'Overview of all entries',
            ],
            'localized' => [
                'de' => [
                    'fields' => [
                        'tagline' => 'Übersicht über alle Seiten',
                    ],
                ],
            ],
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'articleIndex',
            'site' => 'en',
            'title' => 'Articles',
            'slug' => 'articles',
            'parent' => $homepage,
            'localized' => [
                'de' => [
                    'title' => 'Artikel',
                    'slug' => 'artikel',
                ],
            ],
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
                    'slug' => 'themen',
                ],
            ],
        ]);

        $this->createEntry([
            'section' => 'page',
            'type' => 'default',
            'site' => 'en',
            'title' => 'About',
            'slug' => 'about',
            'parent' => $homepage,
            'fields' => [
                'tagline' => 'Who we are',
                'bodyContent' => [
                    [
                        'type' => 'text',
                        'fields' => [
                            'text' => 'TBD.',
                        ],
                    ],
                ],
            ],
            'localized' => [
                'de' => [
                    'title' => 'Über uns',
                    'slug' => 'ueber-uns',
                    'fields' => [
                        'tagline' => 'Wer wir sind',
                    ],
                ],
            ],
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
                    'slug' => 'kontakt',
                ],
            ],
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
                    'slug' => 'suche',
                ],
            ],
        ]);

        $this->createEntry([
            'section' => 'legal',
            'type' => 'default',
            'site' => 'en',
            'title' => 'Imprint',
            'slug' => 'imprint',
            'fields' => [
                'showLink' => 1,
                'bodyContent' => [
                    [
                        'type' => 'text',
                        'fields' => [
                            'text' => 'TBD.',
                        ],
                    ],
                ],
            ],
            'localized' => [
                'de' => [
                    'title' => 'Impressum',
                    'slug' => 'impressum',
                ],
            ],
        ]);

        $this->createEntry([
            'section' => 'legal',
            'type' => 'privacy',
            'site' => 'en',
            'title' => 'Privacy',
            'slug' => 'privacy',
            'fields' => [
                'showLink' => 1,
                'body' => $this->getStarterTextFromFile('privacy_en.md'),
            ],
            'localized' => [
                'de' => [
                    'title' => 'Datenschutz',
                    'slug' => 'datenschutz',
                    'fields' => [
                        'body' => $this->getStarterTextFromFile('privacy_de.md'),
                    ],
                ],
            ],
        ]);

        $this->createEntry([
            'section' => 'legal',
            'type' => 'cookieConsent',
            'site' => 'en',
            'title' => 'This website may use third party cookies.',
            'slug' => 'cookie-consent',
            'fields' => [
                'body' => $this->getStarterTextFromFile('cookiebanner_en.txt'),
            ],
            'localized' => [
                'de' => [
                    'title' => 'Diese Website kann Cookies von Dritten verwenden.',
                    'fields' => [
                        'body' => $this->getStarterTextFromFile('cookiebanner_de.txt'),
                    ],
                ],
            ],
        ]);


        $this->createEntry([
            'section' => 'textModule',
            'type' => 'default',
            'site' => 'en',
            'title' => 'External YouTube Content',
            'slug' => 'youtubeconsent',
            'fields' => [
                'body' => 'This will send personal data to Google/YouTube.',
            ],
            'localized' => [
                'de' => [
                    'title' => 'Externe YouTube-Inhalte',
                    'slug' => 'youtubeconsent',
                    'fields' => [
                        'body' => 'Hiermit werden personenbezogene Daten an Google/YouTube gesendet.',
                    ],
                ],
            ],
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

        // Set admin attributes
        $user = User::find()->one();
        $user->firstName = 'Sabine';
        $user->lastName = 'Mustermann';
        $user->setFieldValue('socialLinks', [
            ['col1' => 'email', 'col2' => 'mailto:sabine.mustermann@example.com'],
        ]);

        Craft::$app->elements->saveElement($user);

        // Add new user
        $user = new User();
        $user->username = 'erna';
        $user->firstName = 'Erna';
        $user->lastName = 'Klawuppke';
        $user->email = 'erna.klawuppke@example.com';
        $user->setFieldValue('socialLinks', [
            ['col1' => 'mastodon', 'col2' => 'https://joinmastodon.org'],
            ['col1' => 'email', 'col2' => 'mailto:erna.klawuppke@example.com'],
        ]);
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

        // Add new user
        $user = new User();
        $user->username = 'klara';
        $user->firstName = 'Klara';
        $user->lastName = 'Peters';
        $user->email = 'klara.peters@example.com';
        $user->setFieldValue('socialLinks', [
            ['col1' => 'mastodon', 'col2' => 'https://joinmastodon.org'],
            ['col1' => 'email', 'col2' => 'mailto:klara.peters@example.com'],
        ]);
        $user->active = true;

        $user->setScenario(User::SCENARIO_LIVE);


        if (Craft::$app->elements->saveElement($user)) {
            $group = Craft::$app->userGroups->getGroupByHandle('writer');

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

    protected function getImagesFromFolder(string $path, $minWidth = null, $limit = null, $orderBy = null)
    {
        if ($orderBy === 'random') {
            $orderBy = Craft::$app->db->driverName === 'mysql' ? 'RAND()' : 'RANDOM()';
        }

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
            ->orderBy($orderBy);


        return $query->collect();
    }

    protected function getMarkdownParagraphs(int $number): string
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

    /**
     * Convert text blocks markdown content to html, so that CKEditor can be used.
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionConvertTextBlocks(): int
    {

        // exit if CKEditor plugin is not installed
        if (!Craft::$app->plugins->isPluginEnabled('ckeditor')) {
            Console::error('CKEditor plugin is not installed and enabled');
            return ExitCode::OK;
        }

        // exit if not confirmed
        if (!$this->confirm('This will convert all text blocks to html, so that CKEditor can be used. Are you sure?')) {
            return ExitCode::OK;
        }

        $this->stdout("Converting text blocks..." . PHP_EOL);

        $textBlocks = MatrixBlock::find()
            ->status(null)
            ->site('*')
            ->field('bodyContent')
            ->type('text')
            ->all();

        foreach ($textBlocks as $textBlock) {
            // Check if text is already converted
            if (str_contains($textBlock->text, '<p>')) {
                continue;
            }

            // output owner title, block id and site name
            $this->stdout($textBlock->owner->title . ' (' . $textBlock->id . ') - ' . $textBlock->site->name);

            $textBlock->text = Markdown::process($textBlock->text, "extra");
            if (!Craft::$app->elements->saveElement($textBlock)) {
                $this->stderr('Error saving text block' . PHP_EOL);
            } else {
                $this->stdout(' - done' . PHP_EOL);
            }
        }

        return ExitCode::OK;
    }
}
