<?php

namespace modules\main\services;

use Craft;
use craft\base\Component;
use yii\helpers\Console;

class BaseService extends Component
{
    protected function info(string $string): void
    {
        if (Craft::$app->request->isConsoleRequest) {
            Console::stdout($string . PHP_EOL);
        } else {
            Craft::info($string, 'MainModule');
        }
    }

    protected function error(string $string): void
    {
        if (Craft::$app->request->isConsoleRequest) {
            Console::stderr($string . PHP_EOL);
        } else {
            Craft::error($string, 'MainModule');
        }
    }
}