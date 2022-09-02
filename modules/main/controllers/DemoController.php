<?php

namespace modules\main\controllers;

use craft\elements\Asset;
use craft\web\Controller;

class DemoController extends Controller
{
    protected int|bool|array $allowAnonymous = true;

    // .../actions/main/demo/say-hello
    public function actionSayHello(): string
    {
        return 'Hello World';
    }
}
