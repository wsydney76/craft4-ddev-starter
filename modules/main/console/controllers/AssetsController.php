<?php

namespace modules\main\console\controllers;

use Craft;
use craft\console\Controller;
use craft\db\Table;
use craft\elements\Entry;
use craft\helpers\App;
use craft\models\Volume;
use GuzzleHttp\Exception\GuzzleException;
use modules\main\helpers\FileHelper;
use yii\console\ExitCode;
use yii\db\Exception;
use function count;
use const PHP_EOL;

class AssetsController extends Controller
{
// This is an example, better call a service method in real live.
    //
    // php craft main/assets/clear-image-transform-directories
    //

    /**
     * Clear all image transform stuff
     *
     * @throws Exception
     */
    public function actionClearImageTransformDirectories(): int
    {

        if (Craft::$app->plugins->isPluginEnabled('imager-x')) {
            $this->stdout("Imager-X is enabled, please use it's utilities to clear the cache.");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (!$this->confirm('This will delete all image transform data', true)) {
            return ExitCode::OK;
        }

        Craft::$app->getDb()->createCommand()
            ->truncateTable(Table::IMAGETRANSFORMINDEX)
            ->execute();

        $volumes = Craft::$app->volumes->getAllVolumes();

        foreach ($volumes as $volume) {
            $this->_clearVolume($volume);
        }

        echo 'Image Transform Directories cleared';
        return ExitCode::OK;
    }

    private function _clearVolume(Volume $volume): void
    {
        $root = App::parseEnv($volume->getTransformFs()->path);

        FileHelper::clearDirectory($root);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDeleteEmptyTransformFolders(): int
    {

        if (Craft::$app->plugins->isPluginEnabled('imager-x')) {
            $this->stdout("Imager-X is enabled, please use it's utilities to clear the cache.");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (!$this->confirm('Delete empty transform subfolders)', true)) {
            return ExitCode::OK;
        }

        FileHelper::cleanupTransformDirectories();

        return ExitCode::OK;
    }

    /**
     * Creates images transforms by requesting each entry
     *
     * php craft main/assets/create-transforms
     *
     * @throws GuzzleException
     */
    public function actionCreateTransforms(): int
    {

        if ($this->interactive && !$this->confirm('Retrieve each page to create missing image sizes? This may take some time.')) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Retrieving pages" . PHP_EOL);

        $client = Craft::createGuzzleClient();

        $entries = Entry::find()
            ->uri(':notempty:')
            ->site('*') // uncomment this line if this is a multi site install
            ->unique() // uncomment this line if there are no site specific images in a multisite install
            ->orderBy('id')
            ->all();

        $c = count($entries);
        $i = 0;
        $errors = 0;

        foreach ($entries as $entry) {
            $i++;
            $this->stdout("[{$i}/{$c}] Id: {$entry->id} {$entry->title} ({$entry->site->name})... ");

            try {
                $result = $client->get($entry->getUrl());
                $this->stdout($result->getStatusCode());
            } catch (\Exception $exception) {
                if ($exception->getCode() == 400) {
                    // Errors can occur if required params are not provided
                    $this->stdout("Error 400, missing params");
                } else {
                    $errors++;
                    $this->stdout("Error {$exception->getMessage()}");
                }
            }

            $this->stdout(PHP_EOL);
        }

        $this->stdout("Done with $errors error(s)" . PHP_EOL);

        return ExitCode::OK;
    }

}
