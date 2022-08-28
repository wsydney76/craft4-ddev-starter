<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\helpers\Assets;
use Faker\Factory;
use yii\console\ExitCode;
use function array_diff;
use function copy;
use function pathinfo;
use function scandir;
use function var_dump;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class InitController extends Controller
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
        $this->actionSetup();
        $this->stdout(PHP_EOL);

        $this->stdout('Create one-off pages...');
        $this->actionCreateEntries();
        $this->stdout(PHP_EOL);

        $this->stdout('Update Users...');
        $this->actionSetUsers();
        $this->stdout(PHP_EOL);

        Craft::$app->runAction('main/seed/create-entries', ['interactive' => $this->interactive]);

        Craft::$app->runAction('main/assets/create-transforms', ['interactive' => $this->interactive]);

        return ExitCode::OK;
    }

    public function actionSetup(): int
    {
        $faker = Factory::create();

        // Set Globals
        $global = GlobalSet::find()->handle('siteInfo')->one();
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
            Craft::$app->elements->saveElement($global);
        }

        return ExitCode::OK;
    }

    public function actionCreateEntries(): int
    {
        $user = User::find()->admin()->one();

        if (Entry::find()->slug('__home__')->exists()) {
            $this->stdout('Entries exist' . PHP_EOL) ;
            return ExitCode::OK;
        }

        // Homepage -----------------------------------------------------------------------------------

        $section = Craft::$app->sections->getSectionByHandle('page');
        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'home');

        $homepage = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'Homepage',
            'slug' => '__home__',
        ]);

        if (!Craft::$app->elements->saveElement($homepage)) {
            echo "Error saving homepage entry\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }


        // News Index -------------------------------------------------------------------------------

        $section = Craft::$app->sections->getSectionByHandle('page');
        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'newsIndex');

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'News',
            'slug' => 'news',
        ]);

        $entry->setParentId($homepage->id);

        if (!Craft::$app->elements->saveElement($entry)) {
            echo "Error saving entry\n";
        } else {
            $this->localize($entry, 'News', 'news');
        }

        // Contact page -------------------------------------------------------------------------------

        $section = Craft::$app->sections->getSectionByHandle('page');
        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'contact');

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'Contact',
            'slug' => 'contact',
        ]);

        $entry->setParentId($homepage->id);

        if (!Craft::$app->elements->saveElement($entry)) {
            echo "Error saving entry\n";
        } else {
            $this->localize($entry, 'Kontakt', 'contact');
        }

        // Search page  -------------------------------------------------------------------------------

        $section = Craft::$app->sections->getSectionByHandle('page');
        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'search');

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'Search',
            'slug' => 'search',
        ]);

        $entry->setParentId($homepage->id);

        if (!Craft::$app->elements->saveElement($entry)) {
            echo "Error saving entry\n";
        } else {
            $this->localize($entry, 'Suche', 'suche');
        }

        // Impressum  -------------------------------------------------------------------------------

        $section = Craft::$app->sections->getSectionByHandle('legal');
        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'default');

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'Imprint',
            'slug' => 'imprint'
        ]);


        if (!Craft::$app->elements->saveElement($entry)) {
            echo "Error saving impressum entry\n";
        } else {
            $this->localize($entry, 'Impressum', 'impressum');
        }

        // Privacy page -------------------------------------------------------------------------------

        $type = ArrayHelper::firstWhere($section->getEntryTypes(), 'handle', 'privacy');

        $entry = new Entry([
            'sectionId' => $section->id,
            'typeId' => $type->id,
            'authorId' => $user->id,
            'title' => 'Privacy Declaration',
            'slug' => 'privacy'
        ]);

        if (!Craft::$app->elements->saveElement($entry)) {
            echo "Error saving privacy entry\n";
        } else {
            $this->localize($entry, 'Datenschutzerklärung', 'datenschutzerklaerung');
        }

        return ExitCode::OK;
    }

    protected function localize($entry, $title, $slug)
    {
        $localizedEntry = $entry->getLocalized()->one();
        if ($localizedEntry) {
            $localizedEntry->title = $title;
            $localizedEntry->slug = $slug;
            Craft::$app->elements->saveElement($localizedEntry);
        }
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
