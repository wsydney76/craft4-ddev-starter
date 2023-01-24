<?php

namespace modules\main\console\controllers;

use craft\console\Controller;
use modules\base\BaseModule;
use modules\main\MainModule;

class BaseController extends Controller
{
    protected function createEntry(array $data, bool $overwrite = false): mixed
    {
        return BaseModule::getInstance()->contentService->createEntry($data, $overwrite);
    }

}

