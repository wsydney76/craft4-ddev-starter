<?php

namespace modules\base\services;

use Craft;
use craft\base\Component;
use yii\helpers\Console;

class BaseService extends Component
{
    protected string $logCategory = 'BaseService';

    protected function info(string $string): void
    {
        if (Craft::$app->request->isConsoleRequest) {
            Console::stdout($string . PHP_EOL);
        } else {
            Craft::info($string, $this->logCategory);
        }
    }

    protected function error(string $string): void
    {
        if (Craft::$app->request->isConsoleRequest) {
            Console::stderr($string . PHP_EOL);
        } else {
            Craft::error($string, $this->logCategory);
        }
    }
}
