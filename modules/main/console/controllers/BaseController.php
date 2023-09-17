<?php

namespace modules\main\console\controllers;

use craft\console\Controller;
use craft\helpers\App;
use modules\base\BaseModule;
use function file_get_contents;

class BaseController extends Controller
{
    protected function createEntry(array $data, bool $overwrite = false): mixed
    {
        return BaseModule::getInstance()->contentService->createEntry($data, $overwrite);
    }

    protected function getStarterTextFromFile(string $string): string
    {
        $path = App::parseEnv('@root') . '/setup/starter-texts/' . $string;

        return file_get_contents($path);
    }
}
