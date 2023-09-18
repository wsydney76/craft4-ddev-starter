<?php

namespace modules\main\console\controllers;

use craft\console\Controller;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidFieldException;
use craft\errors\SiteNotFoundException;
use craft\helpers\App;
use modules\base\BaseModule;
use Throwable;
use yii\base\Exception;
use function file_get_contents;

/**
 * Class BaseController
 * @package modules\main\console\controllers
 */
class BaseController extends Controller
{
    /**
     * @param array $data
     * @param bool $overwrite
     * @return mixed
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws InvalidFieldException
     * @throws SiteNotFoundException
     * @throws Exception
     */
    protected function createEntry(array $data, bool $overwrite = false): mixed
    {
        return BaseModule::getInstance()->contentService->createEntry($data, $overwrite);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function getStarterTextFromFile(string $string): string
    {
        $path = App::parseEnv('@root') . '/setup/starter-texts/' . $string;

        return file_get_contents($path);
    }
}
