<?php

namespace modules\main\console\controllers;

use craft\console\Controller;
use modules\main\MainModule;

class BaseController extends Controller
{
    protected function createEntry(array $data, bool $overwrite = false): mixed
    {
        return MainModule::getInstance()->content->createEntry($data, $overwrite);
    }

}

