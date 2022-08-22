<?php

namespace modules\main\console\controllers;

use craft\console\Controller;
use yii\console\ExitCode;

class DemoController extends Controller
{
    // php craft main/demo/say-hello
    public function actionSayHello(): int
    {
        $this->stdout('Hello World');

        return ExitCode::OK;
    }
}
