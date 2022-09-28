<?php

namespace modules\main\behaviors;

use modules\main\MainModule;
use yii\base\Behavior;

class EntryBehavior extends Behavior
{
    public function test()
    {
        // Example for service call
        return MainModule::getInstance()->content->test();
    }
}